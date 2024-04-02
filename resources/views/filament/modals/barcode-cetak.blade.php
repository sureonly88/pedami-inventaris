<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Layout 2 Kolom x 5 Baris</title>
<style>
    .container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 10px;
    }
    .item {
        border: 1px solid #ccc;
        padding: 20px;
        text-align: center;
    }
    img {
        max-width: 100%;
        height: auto;
    }
</style>
</head>
<body>

<div class="container">
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 1">
        <h2>Tulisan 1</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 2">
        <h2>Tulisan 2</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 3">
        <h2>Tulisan 3</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 4">
        <h2>Tulisan 4</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 5">
        <h2>Tulisan 5</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 6">
        <h2>Tulisan 6</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 7">
        <h2>Tulisan 7</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 8">
        <h2>Tulisan 8</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 9">
        <h2>Tulisan 9</h2>
    </div>
    <div class="item">
        <img src="{{ public_path("img/logo2.png") }}" alt="Gambar 10">
        <h2>Tulisan 10</h2>
    </div>
</div>

</body>
</html>
