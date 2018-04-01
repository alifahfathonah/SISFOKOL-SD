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



require("fpdf/fpdf.php");




////////////////////////////////////

class PDF extends FPDF
{
//variables of html parser
var $B;
var $I;
var $U;
var $HREF;
var $fontList;
var $issetfont;
var $issetcolor;

function PDF($orientation='P',$unit='mm',$format='F4')
	{
	//Call parent constructor
	$this->FPDF($orientation,$unit,$format);
	//Initialization
	$this->B=0;
	$this->I=0;
	$this->U=0;
	$this->HREF='';

	$this->tableborder=0;
	$this->tdbegin=false;
	$this->tdwidth=0;
	$this->tdheight=0;
	$this->tdalign="L";
	$this->tdbgcolor=false;

	$this->oldx=0;
	$this->oldy=0;

	$this->fontlist=array("arial","times","courier","helvetica","symbol");
	$this->issetfont=false;
	$this->issetcolor=false;
	}

//////////////////////////////////////
//html parser

function WriteHTML($html)
	{
	$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
	$html=str_replace("\n",'',$html); //replace carriage returns by spaces
	$html=str_replace("\t",'',$html); //replace carriage returns by spaces
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			elseif($this->tdbegin) {
				if(trim($e)!='' and $e!="&nbsp;") {
					$this->Cell($this->tdwidth,$this->tdheight,$e,$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
				}
				elseif($e=="&nbsp;") {
					$this->Cell($this->tdwidth,$this->tdheight,'',$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
				}
			}
			else
				$this->Write(5,stripslashes(txtentities($e)));
		}
		else
		{
			//Tag
			if($e{0}=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
					if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				$this->OpenTag($tag,$attr);
				}
			}
		}
	}

function OpenTag($tag,$attr)
	{
	//Opening tag
	switch($tag)
		{
		case 'SUP':
			if($attr['SUP'] != '') {
				//Set current font to: Bold, 6pt
				$this->SetFont('','',6);
				//Start 125cm plus width of cell to the right of left margin
				//Superscript "1"
				$this->Cell(2,2,$attr['SUP'],0,0,'L');
			}
			break;

		case 'TABLE': // TABLE-BEGIN
			if( $attr['BORDER'] != '' ) $this->tableborder=$attr['BORDER'];
			else $this->tableborder=0;
			break;
		case 'TR': //TR-BEGIN
			break;
		case 'TD': // TD-BEGIN
			if( $attr['WIDTH'] != '' ) $this->tdwidth=($attr['WIDTH']/4);
			else $this->tdwidth=40; // SET to your own width if you need bigger fixed cells
			if( $attr['HEIGHT'] != '') $this->tdheight=($attr['HEIGHT']/6);
			else $this->tdheight=6; // SET to your own height if you need bigger fixed cells
			if( $attr['ALIGN'] != '' ) {
				$align=$attr['ALIGN'];
				if($align=="LEFT") $this->tdalign="L";
				if($align=="CENTER") $this->tdalign="C";
				if($align=="RIGHT") $this->tdalign="R";
			}
			else $this->tdalign="L"; // SET to your own
			if( $attr['BGCOLOR'] != '' ) {
				$coul=hex2dec($attr['BGCOLOR']);
					$this->SetFillColor($coul['R'],$coul['G'],$coul['B']);
					$this->tdbgcolor=true;
				}
			$this->tdbegin=true;
			break;

		case 'HR':
			if( $attr['WIDTH'] != '' )
				$Width = $attr['WIDTH'];
			else
				$Width = $this->w - $this->lMargin-$this->rMargin;
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetLineWidth(0.2);
			$this->Line($x,$y,$x+$Width,$y);
			$this->SetLineWidth(0.2);
			$this->Ln(1);
			break;
		case 'STRONG':
			$this->SetStyle('B',true);
			break;
		case 'EM':
			$this->SetStyle('I',true);
			break;
		case 'B':
		case 'I':
		case 'U':
			$this->SetStyle($tag,true);
			break;
		case 'A':
			$this->HREF=$attr['HREF'];
			break;
		case 'IMG':
			if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
				if(!isset($attr['WIDTH']))
					$attr['WIDTH'] = 0;
				if(!isset($attr['HEIGHT']))
					$attr['HEIGHT'] = 0;
				$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
			}
			break;
		//case 'TR':
		case 'BLOCKQUOTE':
		case 'BR':
			$this->Ln(5);
			break;
		case 'P':
			$this->Ln(10);
			break;
		case 'FONT':
			if (isset($attr['COLOR']) and $attr['COLOR']!='') {
				$coul=hex2dec($attr['COLOR']);
				$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
				$this->issetcolor=true;
			}
			if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
				$this->SetFont(strtolower($attr['FACE']));
				$this->issetfont=true;
			}
			if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
				$this->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
				$this->issetfont=true;
			}
			break;
		}
	}

function CloseTag($tag)
	{
	//Closing tag
	if($tag=='SUP') {
	}

	if($tag=='TD') { // TD-END
		$this->tdbegin=false;
		$this->tdwidth=0;
		$this->tdheight=0;
		$this->tdalign="L";
		$this->tdbgcolor=false;
	}
	if($tag=='TR') { // TR-END
		$this->Ln();
	}
	if($tag=='TABLE') { // TABLE-END
		//$this->Ln();
		$this->tableborder=0;
	}

	if($tag=='STRONG')
		$tag='B';
	if($tag=='EM')
		$tag='I';
	if($tag=='B' or $tag=='I' or $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
	if($tag=='FONT'){
		if ($this->issetcolor==true) {
			$this->SetTextColor(0);
		}
		if ($this->issetfont) {
			$this->SetFont('arial');
			$this->issetfont=false;
			}
		}
	}

function SetStyle($tag,$enable)
	{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
	}

function PutLink($URL,$txt)
	{
	//Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
	}

//Page header
function HeaderKu()
	{
	//require
	require("../../inc/config.php");
	require("../../inc/koneksi.php");

	//nilai
	$swkd = nosql($_REQUEST['swkd']);
	$kelkd = nosql($_REQUEST['kelkd']);
	$keakd = nosql($_REQUEST['keakd']);
	$tapelkd = nosql($_REQUEST['tapelkd']);
	$kompkd = nosql($_REQUEST['kompkd']);


	//data diri
	$qd = mysql_query("SELECT m_siswa.*, m_siswa.kd AS mskd, siswa_kelas.* ".
				"FROM m_siswa, siswa_kelas ".
				"WHERE siswa_kelas.kd_siswa = m_siswa.kd ".
				"AND siswa_kelas.kd_tapel = '$tapelkd' ".
				"AND siswa_kelas.kd_kelas = '$kelkd' ".
				"AND siswa_kelas.kd_keahlian = '$keakd' ".
				"AND siswa_kelas.kd_keahlian_kompetensi = '$kompkd' ".
				"AND siswa_kelas.kd_siswa = '$swkd'");
	$rd = mysql_fetch_assoc($qd);
	$nama = balikin2($rd['nama']);
	$nis = nosql($rd['nis']);

	//no absen
	$nab = nosql($rd['no_absen']);


	//kelas
	$qk = mysql_query("SELECT * FROM m_kelas ".
						"WHERE kd = '$kelkd'");
	$rk = mysql_fetch_assoc($qk);
	$rkel = nosql($rk['kelas']);


	//keahlian
	$qpro = mysql_query("SELECT * FROM m_keahlian ".
				"WHERE kd = '$keakd'");
	$rpro = mysql_fetch_assoc($qpro);
	$pro_program = balikin($rpro['program']);
	$pro_keah = $pro_program;



	//kompetensi
	$qprgx = mysql_query("SELECT * FROM m_keahlian_kompetensi ".
				"WHERE kd_keahlian = '$keakd' ".
				"AND kd = '$kompkd'");
	$rowprgx = mysql_fetch_assoc($qprgx);
	$prgx_kd = nosql($rowprgx['kd']);
	$prgx_prog = balikin($rowprgx['kompetensi']);
	$prgx_singk = nosql($rowprgx['singkatan']);



	$kelas = $rkel;


	//tapel
	$qtp = mysql_query("SELECT * FROM m_tapel ".
							"WHERE kd = '$tapelkd'");
	$rtp = mysql_fetch_assoc($qtp);
	$thn1 = $rtp['tahun1'];
	$thn2 = $rtp['tahun2'];
	$tapel = "$thn1/$thn2";



	$this->Image(''.$sumber.'/img/logo.jpg',10,8,16);

   	$this->SetFont('Times','B',14);

	$this->SetY(10);
	$this->SetX(27);
	$this->Cell(80,5,''.$sek_nama.'',0,1,'L');

	$this->SetFont('Times','',12);
	$this->SetY(15);
	$this->SetX(27);
	$this->Cell(80,5,''.$sek_alamat.'',0,1,'L');

	$this->SetFont('Times','',12);
	$this->SetY(20);
	$this->SetX(27);
	$this->Cell(80,5,''.$sek_kontak.'',0,1,'L');





	$this->SetY(27);
    	$this->SetFont('Times','',12);
	$this->WriteHTML('<table width="600">'.
						'<tr align="left">'.
						'<td width="130" height="30">Nama</td>'.
						'<td width="10" height="30">: '.$nama.'</td>'.
						'<td width="220" height="30">&nbsp;</td>'.
						'<td width="160" height="30">Program Keahlian</td>'.
						'<td width="3" height="30">: '.$pro_program.'</td>'.
						'</tr><tr>'.
						'<td width="130" height="30">No.Induk</td>'.
						'<td width="10" height="30">: '.$nis.'</td>'.
						'<td width="220" height="30">&nbsp;</td>'.
						'<td width="160" height="30">Kompetensi Keahlian</td>'.
						'<td width="3" height="30">: '.$prgx_singk.'</td>'.
						'</tr>'.
						'</table>'.
						'<table width="600"><tr><td width="600" height="20"><hr></td></tr></table>');

	}
}//end of class





//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['G']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}
?>