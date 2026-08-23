<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$categoryRepository = new CategoryRepository($db);
$productRepository = new ProductRepository($db);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$product = $id > 0 ? $productRepository->find($id) : null;

if ($product === null) {
    Flash::error('Ürün bulunamadı.');
    header('Location: /admin/urunler/liste.php');
    exit;
}

$categories = $categoryRepository->all();
$errors = [];

$name = $product['name'];
$categoryId = (int) $product['category_id'];
$price = $product['price'];
$priceStatus = $product['price_status'] ?? 'fixed';
$description = (string) $product['description'];
$isActive = (bool) $product['is_active'];
$isFeatured = (bool) $product['is_featured'];
$sortOrder = (int) $product['sort_order'];
$metaTitle = (string) $product['meta_title'];
$metaDescription = (string) $product['meta_description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $priceStatus = ($_POST['price_status'] ?? 'fixed') === 'contact' ? 'contact' : 'fixed';
    $priceInput = str_replace(',', '.', trim((string) ($_POST['price'] ?? '')));
    $price = $priceInput;
    $description = trim((string) ($_POST['description'] ?? ''));
    $isActive = isset($_POST['is_active']);
    $isFeatured = isset($_POST['is_featured']);
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $metaTitle = trim((string) ($_POST['meta_title'] ?? ''));
    $metaDescription = trim((string) ($_POST['meta_description'] ?? ''));

    if ($name === '') {
        $errors[] = 'Ürün adı gereklidir.';
    }

    if ($categoryId <= 0 || $categoryRepository->find($categoryId) === null) {
        $errors[] = 'Geçerli bir kategori seçmelisiniz.';
    }

    if ($priceStatus === 'fixed' && ($priceInput === '' || !is_numeric($priceInput) || (float) $priceInput <= 0)) {
        $errors[] = 'Sabit fiyat seçiliyken geçerli (0\'dan büyük) bir fiyat giriniz.';
    }

    $uploader = new ImageUploader(__DIR__ . '/../../uploads');

    if (empty($errors) && !empty($_FILES['main_image']['name'])) {
        try {
            $result = $uploader->upload($_FILES['main_image'], 'products');
            $oldMainImage = $product['main_image'];

            $productRepository->updateMainImage($id, $result['path']);
            $product['main_image'] = $result['path'];

            if (!empty($oldMainImage)) {
                $uploader->delete($oldMainImage);
            }
        } catch (RuntimeException $e) {
            $errors[] = 'Ana görsel: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $productRepository->update($id, [
            'name' => $name,
            'slug' => Slug::unique($db, 'products', Slug::generate($name), $id),
            'description' => $description !== '' ? $description : null,
            'price' => $priceStatus === 'fixed' ? (float) $priceInput : null,
            'price_status' => $priceStatus,
            'category_id' => $categoryId,
            'is_featured' => $isFeatured ? 1 : 0,
            'is_active' => $isActive ? 1 : 0,
            'sort_order' => $sortOrder,
            'meta_title' => $metaTitle !== '' ? $metaTitle : null,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
        ]);

        if (!empty($_FILES['extra_images']['name'][0])) {
            $existingCount = count($productRepository->images($id));
            $fileCount = count($_FILES['extra_images']['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['extra_images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $file = [
                    'name' => $_FILES['extra_images']['name'][$i],
                    'type' => $_FILES['extra_images']['type'][$i],
                    'tmp_name' => $_FILES['extra_images']['tmp_name'][$i],
                    'error' => $_FILES['extra_images']['error'][$i],
                    'size' => $_FILES['extra_images']['size'][$i],
                ];

                try {
                    $result = $uploader->upload($file, 'products');
                    $productRepository->addImage($id, $result['path'], null, $existingCount + $i);
                } catch (RuntimeException $e) {
                    Flash::error('Bir ek görsel yüklenemedi: ' . $e->getMessage());
                }
            }
        }

        Flash::success('Ürün güncellendi.');
        header('Location: /admin/urunler/duzenle.php?id=' . $id);
        exit;
    }
}

$productImages = $productRepository->images($id);

$pageTitle = 'Ürün Düzenle';
$activeMenu = 'urunler';

require __DIR__ . '/../includes/admin-header.php';
?>
<form method="post" action="/admin/urunler/duzenle.php?id=<?php echo $id; ?>" enctype="multipart/form-data" class="admin-form">
    <?php echo Csrf::field(); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="product_id" value="<?php echo $id; ?>">

    <?php foreach ($errors as $error): ?>
        <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <fieldset class="admin-fieldset">
        <legend>1. Görseller</legend>

        <?php if (!empty($product['main_image'])): ?>
            <div class="admin-current-image">
                <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="" class="admin-thumb admin-thumb--large">
                <span>Mevcut ana görsel</span>
            </div>
        <?php endif; ?>

        <label class="admin-field">
            <span>Ana Görseli Değiştir</span>
            <input type="file" name="main_image" accept="image/jpeg,image/png,image/webp">
        </label>

        <?php if (!empty($productImages)): ?>
            <div class="admin-image-grid">
                <?php foreach ($productImages as $image): ?>
                    <div class="admin-image-grid__item">
                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars((string) $image['alt_text']); ?>">
                        <button type="submit" formaction="/admin/urunler/gorsel-sil.php" formmethod="post" formnovalidate
                            name="image_id" value="<?php echo (int) $image['id']; ?>"
                            class="admin-link admin-link--danger"
                            onclick="return confirm('Bu görseli silmek istediğinize emin misiniz?');">Sil</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label class="admin-field">
            <span>Yeni Ek Görsel Ekle (birden fazla seçebilirsiniz)</span>
            <input type="file" name="extra_images[]" accept="image/jpeg,image/png,image/webp" multiple>
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>2. Ürün Adı</legend>
        <label class="admin-field">
            <span>Ürün Adı</span>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>3. Kategori</legend>
        <label class="admin-field">
            <span>Kategori</span>
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int) $category['id']; ?>" <?php echo $categoryId === (int) $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>4. Fiyat</legend>
        <label class="admin-field admin-field--radio">
            <input type="radio" name="price_status" value="fixed" <?php echo $priceStatus === 'fixed' ? 'checked' : ''; ?>>
            <span>Sabit Fiyat</span>
        </label>
        <label class="admin-field admin-field--radio">
            <input type="radio" name="price_status" value="contact" <?php echo $priceStatus === 'contact' ? 'checked' : ''; ?>>
            <span>Fiyat İçin İletişime Geçin (fiyat gösterilmez)</span>
        </label>
        <label class="admin-field">
            <span>Fiyat (₺)</span>
            <input type="text" name="price" value="<?php echo htmlspecialchars((string) $price); ?>" inputmode="decimal" placeholder="Sadece 'Sabit Fiyat' seçiliyken doldurun">
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>5. Açıklama</legend>
        <label class="admin-field">
            <span>Ürün Açıklaması</span>
            <textarea name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>6. Görünürlük</legend>
        <label class="admin-field admin-field--checkbox">
            <input type="checkbox" name="is_active" <?php echo $isActive ? 'checked' : ''; ?>>
            <span>Aktif (sitede görünür)</span>
        </label>
        <label class="admin-field admin-field--checkbox">
            <input type="checkbox" name="is_featured" <?php echo $isFeatured ? 'checked' : ''; ?>>
            <span>Öne Çıkan Ürün</span>
        </label>
        <label class="admin-field">
            <span>Sıralama Değeri</span>
            <input type="number" name="sort_order" value="<?php echo $sortOrder; ?>" min="0">
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>7. SEO Alanları (opsiyonel)</legend>
        <label class="admin-field">
            <span>Meta Başlık</span>
            <input type="text" name="meta_title" value="<?php echo htmlspecialchars($metaTitle); ?>">
        </label>
        <label class="admin-field">
            <span>Meta Açıklama</span>
            <textarea name="meta_description" rows="3"><?php echo htmlspecialchars($metaDescription); ?></textarea>
        </label>
    </fieldset>

    <div class="admin-form__actions">
        <button type="submit" class="admin-btn admin-btn--primary">Güncelle</button>
        <a href="/admin/urunler/liste.php" class="admin-btn admin-btn--ghost">Vazgeç</a>
    </div>
</form>
<?php
require __DIR__ . '/../includes/admin-footer.php';
