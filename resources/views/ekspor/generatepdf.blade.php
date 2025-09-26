<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Page</title>
</head>
<body>
    <!-- Container untuk menampilkan data yang diambil -->
    <div id="resultContainer"></div>

    <script>
        // Mengambil data menggunakan fetch (permintaan GET)
        fetch('/save-summernote-show')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Data:', data);
                // Lakukan sesuatu dengan data yang diambil, misalnya menampilkan di dalam elemen HTML
                document.getElementById('resultContainer').innerHTML = data.content;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    </script>
</body>
</html>
