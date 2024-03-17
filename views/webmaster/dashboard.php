<!-- dashboard.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webmaster Dashboard</title>
    <!-- Подключение Bootstrap -->

</head>
<body>
    <div class="container mt-5">
        <h2>Webmaster Dashboard</h2>
        <div class="row">
            <div class="col-md-6">
                <h3>My Offers</h3>
                <!-- Таблица со списком доступных офферов для веб-мастера -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Offer Name</th>
                            <th>Cost Per Click</th>
                            <th>Target URL</th>
                            <th>Themes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer) : ?>
                            <tr>
                                <td><?php echo $offer['id']; ?></td>
                                <td><?php echo $offer['offer_name']; ?></td>
                                <td><?php echo $offer['cost_per_click']; ?></td>
                                <td><?php echo $offer['target_url']; ?></td>
                                <td><?php echo $offer['themes']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
