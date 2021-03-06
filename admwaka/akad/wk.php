<?php
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
/////// SISFOKOL_SD_v5.0_(PernahJaya)                          ///////
/////// (Sistem Informasi Sekolah untuk SD)                    ///////
///////////////////////////////////////////////////////////////////////
/////// Dibuat oleh :                                           ///////
/////// Agus Muhajir, S.Kom                                     ///////
/////// URL 	:                                               ///////
///////     * http://omahbiasawae.com/                          ///////
///////     * http://sisfokol.wordpress.com/                    ///////
///////     * http://hajirodeon.wordpress.com/                  ///////
///////     * http://yahoogroup.com/groups/sisfokol/            ///////
///////     * http://yahoogroup.com/groups/linuxbiasawae/       ///////
/////// E-Mail	:                                               ///////
///////     * hajirodeon@yahoo.com                              ///////
///////     * hajirodeon@gmail.com                              ///////
/////// HP/SMS/WA : 081-829-88-54                               ///////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////



session_start();

//fungsi - fungsi
require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/cek/admwaka.php");
$tpl = LoadTpl("../../template/index.html");


nocache;

//nilai
$filenya = "wk.php";
$judul = "Wali Kelas";
$judulku = "[$waka_session : $nip10_session.$nm10_session] ==> $judul";
$judulx = $judul;
$tapelkd = nosql($_REQUEST['tapelkd']);
$kelkd = nosql($_REQUEST['kelkd']);
$ke = "$filenya?tapelkd=$tapelkd";





//focus...
if (empty($tapelkd))
	{
	$diload = "document.formx.tapel.focus();";
	}







//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//jika batal
if ($_POST['btnBTL'])
	{
	//nilai
	$tapelkd = nosql($_POST['tapelkd']);

	//diskonek
	xfree($qbw);
	xclose($koneksi);

	//re-direct
	xloc($ke);
	exit();
	}


//jika hapus
if ($_POST['btnHPS'])
	{
	$jml = nosql($_POST['jml']);
	$tapelkd = nosql($_POST['tapelkd']);


	//ambil semua
	for ($i=1; $i<=$jml;$i++)
		{
		//ambil nilai
		$yuk = "item";
		$yuhu = "$yuk$i";
		$kdix = nosql($_POST["$yuhu"]);

		//del
		mysql_query("DELETE FROM m_walikelas ".
				"WHERE kd = '$kdix'");
		}

	//diskonek
	xfree($qbw);
	xclose($koneksi);

	//re-direct
	xloc($ke);
	exit();
	}


//jika simpan
if ($_POST['btnTBH'])
	{
	//nilai
	$tapelkd = nosql($_POST['tapelkd']);
	$kelas = nosql($_POST['kelas']);
	$pegawai = nosql($_POST['pegawai']);


	//nek nul
	if ((empty($pegawai)) OR (empty($kelas)))
		{
		//diskonek
		xfree($qbw);
		xclose($koneksi);

		//re-direct
		$pesan = "Input Tidak Lengkap. Harap Diperhatikan...!";
		pekem($pesan,$ke);
		exit();
		}
	else
		{
		//deteksi
		$qcc = mysqL_query("SELECT m_walikelas.*, m_pegawai.* ".
							"FROM m_walikelas, m_pegawai ".
							"WHERE m_walikelas.kd_pegawai = m_pegawai.kd ".
							"AND m_walikelas.kd_tapel = '$tapelkd' ".
							"AND m_walikelas.kd_kelas = '$kelas'");
		$rcc = mysql_fetch_assoc($qcc);
		$tcc = mysql_num_rows($qcc);


		//nek iya
		if ($tcc != 0)
			{
			//diskonek
			xfree($qbw);
			xclose($koneksi);

			//re-direct
			$pesan = "Sudah Ada WaliKelas Untuk Kelas Ini. Silahkan Diganti...!";
			pekem($pesan,$ke);
			exit();
			}
		else
			{
			//query
			mysql_query("INSERT INTO m_walikelas(kd, kd_tapel, kd_kelas, kd_pegawai) VALUES ".
							"('$x', '$tapelkd', '$kelas', '$pegawai')");

			//diskonek
			xfree($qbw);
			xclose($koneksi);

			//re-direct
			xloc($ke);
			exit();
			}
		}
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



//isi *START
ob_start();

//menu
require("../../inc/menu/admwaka.php");

//isi_menu
$isi_menu = ob_get_contents();
ob_end_clean();




//isi *START
ob_start();

//js
require("../../inc/js/jumpmenu.js");
require("../../inc/js/swap.js");
require("../../inc/js/number.js");
require("../../inc/js/checkall.js");
xheadline($judul);


//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<form name="formx" method="post" action="'.$filenya.'">
<table bgcolor="'.$warnaover.'" width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>
Tahun Pelajaran : ';
echo "<select name=\"tapel\" onChange=\"MM_jumpMenu('self',this,0)\">";

//terpilih
$qtpx = mysql_query("SELECT * FROM m_tapel ".
			"WHERE kd = '$tapelkd'");
$rowtpx = mysql_fetch_assoc($qtpx);
$tpxkd = nosql($rowtpx['kd']);
$tpxtahun1 = nosql($rowtpx['tahun1']);
$tpxtahun2 = nosql($rowtpx['tahun2']);

echo '<option value="'.$tpxkd.'">'.$tpxtahun1.'/'.$tpxtahun2.'</option>';

$qtp = mysql_query("SELECT * FROM m_tapel ".
			"WHERE kd <> '$tapelkd' ".
			"ORDER BY tahun1 ASC");
$rowtp = mysql_fetch_assoc($qtp);

do
	{
	$tpkd = nosql($rowtp['kd']);
	$tptahun1 = nosql($rowtp['tahun1']);
	$tptahun2 = nosql($rowtp['tahun2']);

	echo '<option value="'.$filenya.'?tapelkd='.$tpkd.'">'.$tptahun1.'/'.$tptahun2.'</option>';
	}
while ($rowtp = mysql_fetch_assoc($qtp));

echo '</select>
</td>
</tr>
</table>
<br>';


//nek drg
if (empty($tapelkd))
	{
	echo '<h4>
	<font color="#FF0000"><strong>TAHUN PELAJARAN Belum Diplih...!</strong></font>
	</h4>';
	}

else
	{
	//query
	$q = mysql_query("SELECT m_walikelas.*, m_walikelas.kd AS mwkd, ".
						"m_pegawai.*, m_pegawai.kd AS mpkd, m_kelas.* ".
						"FROM m_walikelas, m_pegawai, m_kelas ".
						"WHERE m_walikelas.kd_pegawai = m_pegawai.kd ".
						"AND m_walikelas.kd_kelas = m_kelas.kd ".
						"AND m_walikelas.kd_tapel = '$tapelkd' ".
						"ORDER BY round(m_kelas.no) ASC");
	$row = mysql_fetch_assoc($q);
	$total = mysql_num_rows($q);


	//penambahan
	echo '<select name="pegawai">
	<option value="" selected>-Pegawai-</option>';

	//data pegawai
	$qpeg = mysql_query("SELECT * FROM m_pegawai ".
				"ORDER BY round(nip) ASC");
	$rpeg = mysql_fetch_assoc($qpeg);

	do
		{
		$peg_kd = nosql($rpeg['kd']);
		$peg_nip = nosql($rpeg['nip']);
		$peg_nm = balikin($rpeg['nama']);

		echo '<option value="'.$peg_kd.'">'.$peg_nip.'. '.$peg_nm.'</option>';
		}
	while ($rpeg = mysql_fetch_assoc($qpeg));


	echo '</select>,
	<select name="kelas">
	<option value="" selected>-Kelas-</option>';
	$qrung = mysql_query("SELECT * FROM m_kelas ".
							"ORDER BY round(no) ASC, ".
							"kelas ASC");
	$rrung = mysql_fetch_assoc($qrung);

	do
		{
		$rung_kd = nosql($rrung['kd']);
		$rung_kelas = balikin($rrung['kelas']);

		echo '<option value="'.$rung_kd.'">'.$rung_kelas.'</option>';
		}
	while ($rrung = mysql_fetch_assoc($qrung));


	echo '</select>


	<input name="btnTBH" type="submit" value="Tambah >>">';

	//detail
	echo '<table width="600" border="1" cellspacing="0" cellpadding="3">
	<tr bgcolor="'.$warnaheader.'">
	<td width="1">&nbsp;</td>
	<td width="50"><strong><font color="'.$warnatext.'">Kelas</font></strong></td>
	<td width="100"><strong><font color="'.$warnatext.'">NIP</font></strong></td>
	<td><strong><font color="'.$warnatext.'">Nama</font></strong></td>
	</tr>';

	//nek ada
	if ($total != 0)
		{
		do
			{
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				}


			//nilai
			$i_nomer = $i_nomer + 1;
			$i_kd = nosql($row['mwkd']);
			$i_mpkd = nosql($row['mpkd']);
			$i_nip = nosql($row['nip']);
			$i_nama = balikin($row['nama']);
			$i_kelas = balikin($row['kelas']);


			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td><input name="kd'.$i_nomer.'" type="hidden" value="'.$i_kd.'">
			<input type="checkbox" name="item'.$i_nomer.'" value="'.$i_kd.'">
		        </td>
			<td>'.$i_kelas.'</td>
			<td>'.$i_nip.'</td>
			<td>'.$i_nama.'</td>
			</tr>';
			}
		while ($row = mysql_fetch_assoc($q));
		}

	echo '</table>
	<table width="600" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td width="250">
	<input name="tapelkd" type="hidden" value="'.$tapelkd.'">
	<input name="jml" type="hidden" value="'.$limit.'">
	<input name="btnALL" type="button" value="SEMUA" onClick="checkAll('.$limit.')">
	<input name="btnBTL" type="reset" value="BATAL">
	<input name="btnHPS" type="submit" value="HAPUS">
	</td>
	<td align="right"><strong><font color="#FF0000">'.$total.'</font></strong> Data. '.$pagelist.'</td>
	</tr>
	</table>';
	}

echo '</form>
<br>
<br>
<br>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");


//diskonek
xfree($qbw);
xclose($koneksi);
exit();
?>