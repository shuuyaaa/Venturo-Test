<?php
function fetchMenuData() {
    return json_decode(file_get_contents("http://tes-web.landa.id/intermediate/menu"), true);
}

function fetchTransaksiData($tahun) {
    return json_decode(file_get_contents("http://tes-web.landa.id/intermediate/transaksi?tahun=" . $tahun), true);
}

function initializeMenuData($menu) {
    $values = array_fill(0, 12, 0);
    foreach ($menu as $key => $value) {
        $menu[$key]['value'] = $values;
        $menu[$key]['totalHarga'] = 0;
    }
    return $menu;
}

function calculateTotals($menu, $transaksi) {
    $monthlyTotals = array_fill(0, 12, 0);
    $yearlyTotal = 0;

    foreach ($transaksi as $key => $value) {
        $valueTrans = $value;
        $harga = $value['total'];
        $dateFormat = DateTime::createFromFormat("Y-m-d", $value['tanggal']);
        $bulan = $dateFormat->format("n");

        foreach ($menu as $key => $value) {
            if ($value['menu'] === $valueTrans['menu']) {
                $menu[$key]['value'][$bulan - 1] += $valueTrans['total'];
                $menu[$key]['totalHarga'] += $valueTrans['total'];
            }
        }

        $monthlyTotals[$bulan - 1] += $valueTrans['total'];
        $yearlyTotal += $valueTrans['total'];
    }

    return [$menu, $monthlyTotals, $yearlyTotal];
}

if (isset($_GET['tahun']) && $_GET['tahun'] != "") {
    $tahun = $_GET['tahun'];
    $menu = fetchMenuData();
    $transaksi = fetchTransaksiData($tahun);
    $menu = initializeMenuData($menu);
    list($menu, $monthlyTotals, $yearlyTotal) = calculateTotals($menu, $transaksi);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Vtest</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        td,
        th {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="card" style="margin: 2rem 0rem;">
            <div class="card-header">
                Venturo - Laporan penjualan tahunan per menu
            </div>
            <div class="card-body">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <select id="my-select" class="form-control" name="tahun">
                                    <option value="">Pilih Tahun</option>
                                    <option value="2021" <?= isset($_GET['tahun']) && $_GET['tahun'] == '2021' ? 'selected' : '' ?>>2021</option>
                                    <option value="2022" <?= isset($_GET['tahun']) && $_GET['tahun'] == '2022' ? 'selected' : '' ?>>2022</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary">
                                Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" style="margin: 0;">
                        <thead>
                            <tr class="table-dark">
                                <th rowspan="2" style="text-align:center;vertical-align: middle;width: 250px;">Menu</th>
                                <th colspan="12" style="text-align: center;">Periode Pada
                                    <?= $_GET['tahun'] ?>
                                </th>
                                <th rowspan="2" style="text-align:center;vertical-align: middle;width:75px">Total</th>
                            </tr>
                            <tr class="table-dark">
                                <th style="text-align: center;width: 75px;">Jan</th>
                                <th style="text-align: center;width: 75px;">Feb</th>
                                <th style="text-align: center;width: 75px;">Mar</th>
                                <th style="text-align: center;width: 75px;">Apr</th>
                                <th style="text-align: center;width: 75px;">Mei</th>
                                <th style="text-align: center;width: 75px;">Jun</th>
                                <th style="text-align: center;width: 75px;">Jul</th>
                                <th style="text-align: center;width: 75px;">Ags</th>
                                <th style="text-align: center;width: 75px;">Sep</th>
                                <th style="text-align: center;width: 75px;">Okt</th>
                                <th style="text-align: center;width: 75px;">Nov</th>
                                <th style="text-align: center;width: 75px;">Des</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <?php if (isset($_GET['tahun']) && $_GET['tahun'] != ""): ?>
    <tr>
        <td class="table-secondary" colspan="14"><b>Makanan</b></td>
    </tr>
    <?php
    foreach ($menu as $key => $value):
        if ($value['kategori'] === "makanan"):
            ?>
            <tr>
                <td style="text-align: left;">
                    <?= $menu[$key]['menu'] ?>
                </td>
                <?php
                foreach ($value['value'] as $kunci => $nilai):
                    ?>
                    <td style="text-align: right;">
                        <?= $nilai != 0 ? number_format($nilai) : "" ?>
                    </td>
                    <?php
                endforeach;
                ?>

                <td style="text-align: right;"><b>
                        <?= number_format($value['totalHarga']) ?>
                    </b></td>
            </tr>
            <?php
        endif;
    endforeach;
    ?>
    <tr>
        <td class="table-secondary" colspan="14"><b>Minuman</b></td>
    </tr>
    <?php
    foreach ($menu as $key => $value):
        if ($value['kategori'] === "minuman"):
            ?>
            <tr>
                <td style="text-align: left;">
                    <?= $menu[$key]['menu'] ?>
                </td>
                <?php
                foreach ($value['value'] as $kunci => $nilai):
                    ?>
                    <td style="text-align: right;">
                        <?= $nilai != 0 ? number_format($nilai) : "" ?>
                    </td>
                    <?php
                endforeach;
                ?>

                <td style="text-align: right;"><b>
                        <?= number_format($value['totalHarga']) ?>
                    </b></td>
            </tr>
            <?php
        endif;
    endforeach;
    ?>
    <tr>
        <td class="table-dark" colspan="1"><b>Total</b></td>
        <?php
        foreach ($monthlyTotals as $total):
            ?>
            <td class="table-dark" style="text-align: right;">
                <b>
                    <?php
                    // Tambahkan kondisi untuk menampilkan hanya jika total tidak sama dengan 0
                    echo $total !== 0 ? number_format($total) : "";
                    ?>
                </b>
            </td>
            <?php
        endforeach;
        ?>
        <td class="table-dark" style="text-align: right;" colspan="1"><b>
                <?= number_format($yearlyTotal) ?>
            </b></td>
    </tr>
<?php else: ?>
<?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>