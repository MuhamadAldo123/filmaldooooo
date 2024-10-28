<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Data array asosiatif statis
$data = [
    [
        "id" => 1,
        "judul" => "Inception",
        "genre" => "Sci-Fi",
        "popularitas" => 90,
        "rating" => 4.8,
        "tahun_rilis" => 2010,
        "pemeran_utama" => "Leonardo DiCaprio"
    ],
    [
        "id" => 2,
        "judul" => "The Godfather",
        "genre" => "Drama",
        "popularitas" => 95,
        "rating" => 4.9,
        "tahun_rilis" => 1972,
        "pemeran_utama" => "Marlon Brando"
    ],
    [
        "id" => 3,
        "judul" => "Interstellar",
        "genre" => "Sci-Fi",
        "popularitas" => 85,
        "rating" => 4.7,
        "tahun_rilis" => 2014,
        "pemeran_utama" => "Matthew McConaughey"
    ],
    [
        "id" => 4,
        "judul" => "Avengers: Endgame",
        "genre" => "Action",
        "popularitas" => 92,
        "rating" => 4.6,
        "tahun_rilis" => 2019,
        "pemeran_utama" => "Robert Downey Jr."
    ],
    [
        "id" => 5,
        "judul" => "Toy Story 3",
        "genre" => "Animation",
        "popularitas" => 80,
        "rating" => 4.5,
        "tahun_rilis" => 2010,
        "pemeran_utama" => "Tom Hanks"
    ]
];

// Fungsi Quick Sort
function quickSort($data, $key) {
    if (count($data) < 2) {
        return $data;
    }
    
    $left = $right = [];
    $pivot = $data[0];
    
    for ($i = 1; $i < count($data); $i++) {
        if ($data[$i][$key] > $pivot[$key]) {
            $left[] = $data[$i];
        } else {
            $right[] = $data[$i];
        }
    }
    
    return array_merge(quickSort($left, $key), [$pivot], quickSort($right, $key));
}

// Cek dan pilih metode pengurutan berdasarkan input
if (isset($_GET['sort'])) {
    $sortKey = $_GET['sort'];
    $validSortKeys = ['rating', 'popularitas', 'tahun_rilis'];
    
    if (in_array($sortKey, $validSortKeys)) {
        $data = quickSort($data, $sortKey);
    } else {
        echo "Kriteria pengurutan tidak valid.";
    }
}

// Filter berdasarkan genre
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : '';
function filterByGenre($data, $genre) {
    if (empty($genre)) {
        return $data;
    }
    return array_filter($data, function ($item) use ($genre) {
        return $item['genre'] === $genre;
    });
}
$data = filterByGenre($data, $selectedGenre);

// Pagination
$itemsPerPage = 10;
$totalItems = count($data);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startIndex = ($currentPage - 1) * $itemsPerPage;
$pagedData = array_slice($data, $startIndex, $itemsPerPage);

// Upload gambar
$uploadedImagePath = ''; // Variabel untuk menyimpan path gambar yang diupload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES["fileToUpload"])) {
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File bukan gambar.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "File sudah ada.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "File terlalu besar.";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "File " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " telah di-upload.";
            $uploadedImagePath = $target_file; // Simpan path gambar
        } else {
            echo "Terjadi kesalahan saat meng-upload file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Film</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff; /* Warna latar belakang halaman */
            color: #333;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }
        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        label {
            font-weight: bold;
            color: #333;
        }
        select, input[type="file"], button, input[type="submit"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff; /* Warna latar belakang header tabel */
            color: #fff; /* Warna teks header tabel */
        }
        tr:nth-child(even) {
            background-color: #f9f9f9; /* Warna latar belakang untuk baris genap */
        }
        tr:hover {
            background-color: #e9e9e9; /* Warna latar belakang saat baris di-hover */
        }
        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            background-color: #f2f2f2;
            border-radius: 3px;
            color: #333;
        }
        .pagination a.active {
            font-weight: bold;
            background-color: #007bff;
            color: #fff;
        }
        .center {
            text-align: center;
        }
        .uploaded-image {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Daftar Film</h2>
    
    <form method="get" action="">
        <label for="sort">Urutkan Berdasarkan:</label>
        <select name="sort" id="sort">
            <option value="rating">Rating</option>
            <option value="popularitas">Popularitas</option>
            <option value="tahun_rilis">Tahun Rilis</option>
        </select>
        <label for="genre">Pilih Genre:</label>
        <select name="genre" id="genre">
            <option value="">Semua Genre</option>
            <option value="Sci-Fi">Sci-Fi</option>
            <option value="Drama">Drama</option>
            <option value="Action">Action</option>
            <option value="Animation">Animation</option>
        </select>
        <button type="submit">Filter</button>
    </form>
    
    <form method="post" action="" enctype="multipart/form-data" class="center">
        <label for="fileToUpload">Upload Gambar:</label>
        <input type="file" name="fileToUpload" id="fileToUpload" required>
        <input type="submit" value="Upload Gambar">
    </form>

    <?php if ($uploadedImagePath): ?>
        <img src="<?php echo htmlspecialchars($uploadedImagePath); ?>" alt="Uploaded Image" class="uploaded-image">
    <?php endif; ?>

    <table>
        <tr>
            <th>Judul</th>
            <th>Genre</th>
            <th>Popularitas</th>
            <th>Rating</th>
            <th>Tahun Rilis</th>
            <th>Pemeran Utama</th>
        </tr>

        <?php if (!empty($pagedData)) : ?>
            <?php foreach ($pagedData as $film) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($film["judul"]); ?></td>
                    <td><?php echo htmlspecialchars($film["genre"]); ?></td>
                    <td><?php echo htmlspecialchars($film["popularitas"]); ?></td>
                    <td><?php echo htmlspecialchars($film["rating"]); ?></td>
                    <td><?php echo htmlspecialchars($film["tahun_rilis"]); ?></td>
                    <td><?php echo htmlspecialchars($film["pemeran_utama"]); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6" class="center">Tidak ada film yang ditemukan.</td>
            </tr>
        <?php endif; ?>
    </table>

    <div class="pagination center">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>&sort=<?php echo htmlspecialchars($sortKey); ?>&genre=<?php echo htmlspecialchars($selectedGenre); ?>" class="<?php echo $i == $currentPage ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>
