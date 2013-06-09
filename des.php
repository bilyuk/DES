<?

if(strlen($_GET['source']) != '8') exit ('strlen source <> 8');
if(strlen($_GET['key']) != '8') exit ('strlen key <> 8');

$hexSource = $_GET['source']; // 16 символов
$key = $_GET['key']; // 8 символов

function shiftleft($keyPC1) {
	return substr($keyPC1, 1, (strlen($keyPC1) - 1)) . substr($keyPC1, 0, 1);
}

function move($array, $data) {
	$rs = '';
	foreach($array as $index) {
		$rs .= $data[$index-1];
	}

	return $rs;
}

function dataXOR($data1, $data2) {
	if(strlen($data1) != strlen($data2)) exit('xor err');

	$rs = '';
	for($i=0; $i<strlen($data1); $i++) {
		if(($data1[$i] == '1' or $data2[$i] == '1') and ($data1[$i] == '0' or $data2[$i] == '0')) $logic = '1';
		else $logic = '0';
		$rs .= $logic;
	}

	return $rs;
}

function sbox($EL) {
	$S = array(
		1 => array(
			0 => array(14, 4, 13, 1, 2, 15, 11, 8, 3, 10, 6, 12, 5, 9, 0, 7),
			1 => array(0, 15, 7, 4, 14, 2, 13, 1, 10, 6, 12, 11, 9, 5, 3, 8),
			2 => array(4, 1, 14, 8, 13, 6, 2, 11, 15, 12, 9, 7, 3, 10, 5, 0),
			3 => array(15, 12, 8, 2, 4, 9, 1, 7, 5, 11, 3, 14, 10, 0, 6, 13),
		),
		2 => array(
			0 => array(15, 1, 8, 14, 6, 11, 3, 4, 9, 7, 2, 13, 12, 0, 5, 10),
			1 => array(3, 13, 4, 7, 15, 2, 8, 14, 12, 0, 1, 10, 6, 9, 11, 5),
			2 => array(0, 14, 7, 11, 10, 4, 13, 1, 5, 8, 12, 6, 9, 3, 2, 15),
			3 => array(13, 8, 10, 1, 3, 15, 4, 2, 11, 6, 7, 12, 0, 5, 14, 9),
		),
		3 => array(
			0 => array(10, 0, 9, 14, 6, 3, 15, 5, 1, 13, 12, 7, 11, 4, 2, 8),
			1 => array(13, 7, 0, 9, 3, 4, 6, 10, 2, 8, 5, 14, 12, 11, 15, 1),
			2 => array(13, 6, 4, 9, 8, 15, 3, 0, 11, 1, 2, 12, 5, 10, 14, 7),
			3 => array(1, 10, 13, 0, 6, 9, 8, 7, 4, 15, 14, 3, 11, 5, 2, 12),
		),
		4 => array(
			0 => array(7, 13, 14, 3, 0, 6, 9, 10, 1, 2, 8, 5, 11, 12, 4, 15),
			1 => array(13, 8, 11, 5, 6, 15, 0, 3, 4, 7, 2, 12, 1, 10, 14, 9),
			2 => array(10, 6, 9, 0, 12, 11, 7, 13, 15, 1, 3, 14, 5, 2, 8, 4),
			3 => array(3, 15, 0, 6, 10, 1, 13, 8, 9, 4, 5, 11, 12, 7, 2, 14),
		),
		5 => array(
			0 => array(2, 12, 4, 1, 7, 10, 11, 6, 8, 5, 3, 15, 13, 0, 14, 9),
			1 => array(14, 11, 2, 12, 4, 7, 13, 1, 5, 0, 15, 10, 3, 9, 8, 6),
			2 => array(4, 2, 1, 11, 10, 13, 7, 8, 15, 9, 12, 5, 6, 3, 0, 14),
			3 => array(11, 8, 12, 7, 1, 14, 2, 13, 6, 15, 0, 9, 10, 4, 5, 3),
		),
		6 => array(
			0 => array(12, 1, 10, 15, 9, 2, 6, 8, 0, 13, 3, 4, 14, 7, 5, 11),
			1 => array(10, 15, 4, 2, 7, 12, 9, 5, 6, 1, 13, 14, 0, 11, 3, 8),
			2 => array(9, 14, 15, 5, 2, 8, 12, 3, 7, 0, 4, 10, 1, 13, 11, 6),
			3 => array(4, 3, 2, 12, 9, 5, 15, 10, 11, 14, 1, 7, 6, 0, 8, 13),
		),
		7 => array(
			0 => array(4, 11, 2, 14, 15, 0, 8, 13, 3, 12, 9, 7, 5, 10, 6, 1),
			1 => array(13, 0, 11, 7, 4, 9, 1, 10, 14, 3, 5, 12, 2, 15, 8, 6),
			2 => array(1, 4, 11, 13, 12, 3, 7, 14, 10, 15, 6, 8, 0, 5, 9, 2),
			3 => array(6, 11, 13, 8, 1, 4, 10, 7, 9, 5, 0, 15, 14, 2, 3, 12),
		),
		8 => array(
			0 => array(13, 2, 8, 4, 6, 15, 11, 1, 10, 9, 3, 14, 5, 0, 12, 7),
			1 => array(1, 15, 13, 8, 10, 3, 7, 4, 12, 5, 6, 11, 0, 14, 9, 2),
			2 => array(7, 11, 4, 1, 9, 12, 14, 2, 0, 6, 10, 13, 15, 3, 5, 8),
			3 => array(2, 1, 14, 7, 4, 10, 8, 13, 15, 12, 9, 0, 3, 5, 6, 11),
		),
	);

	$B = array();
	$B[1] = substr($EL, 0, 6);
	$B[2] = substr($EL, 6, 6);
	$B[3] = substr($EL, 12, 6);
	$B[4] = substr($EL, 18, 6);
	$B[5] = substr($EL, 24, 6);
	$B[6] = substr($EL, 30, 6);
	$B[7] = substr($EL, 36, 6);
	$B[8] = substr($EL, 42, 6);

	$BI = array();
	foreach($B as $id => $row) {
		$abin = $row[0] . $row[5];
		$a = base_convert($abin, 2, 10);

		$bbin = substr($row, 1, 4);
		$b = base_convert($bbin, 2, 10);

		$BICurrent = base_convert($S[$id][$a][$b], 10, 2);

		if(strlen($BICurrent) < 4) {
			while(strlen($BICurrent) < 4) {
				$BICurrent = 0 . $BICurrent;
			}
		}

		$BI[$id] = $BICurrent;
	}

	return implode('', $BI);
}

$PC1 = array(
57, 49, 41, 33, 25,    17,    9,
 1, 58,    50,   42,    34,    26,   18,
10,    2,    59,   51,    43,    35,   27,
19,   11,     3,   60,    52,    44,   36,
63,   55,    47,   39,    31,    23,   15,
 7,   62,    54,   46,    38,    30,   22,
14,    6,    61,   53,    45,    37,   29,
21,   13,     5,   28,    20,    12,    4);

$PC2 = array(
14,    17,   11,    24,     1,    5,
 3,    28,   15,     6,    21,   10,
23 ,   19,   12,     4,    26,    8,
16,     7 ,  27 ,   20,   13,    2,
41,    52,   31,    37,    47,   55,
30,    40,   51,    45,    33,   48,
44,    49,   39,    56,    34,   53,
46,    42,   50,    36,    29,   32,
);

$r = array(
1 => 1,
2 =>1,
3 => 2,
4=> 2,
5=> 2,
6=>2,
7=>2,
8=>2,
9=>1,
10=>2,
11=>2,
12=>2,
13=>2,
14=>2,
15=>2,
16=>1,
);

// генерация ключей

$rs = array();

$binKey = '';
for($i=0; $i<strlen($key); $i++) {
	$num = ord($key[$i]);

	$bin = base_convert($num, 10, 2);

	if(strlen($bin) < 8) {
		while(strlen($bin) < 8) {
			$bin = 0 . $bin;
		}
	}

	$rs['binKey'] = $binKey .= $bin;
}

$keyPC1 = '';
foreach($PC1 as $index) {
	$rs['keyPermutation1'] = $keyPC1 .= $binKey[$index-1];
}


$C[0] = substr($keyPC1, 0, 28);
$D[0] = substr($keyPC1, 28, 28);

for($i=1; $i<=16; $i++) {
	$shift = $C[$i-1];

	for($shiftIndex = 1; $shiftIndex <= $r[$i]; $shiftIndex++) {
		$shift = shiftleft($shift);
	}
	$C[$i] = $shift;
}

for($i=1; $i<=16; $i++) {
	$shift = $D[$i-1];

	for($shiftIndex = 1; $shiftIndex <= $r[$i]; $shiftIndex++) {
		$shift = shiftleft($shift);
	}
	$D[$i] = $shift;
}

$k = array();
for($i=1; $i<=16; $i++) {
	$concat = $C[$i] . $D[$i];

	$k[$i] = '';
	foreach($PC2 as $index) {
		$k[$i] .= $concat[$index-1];
	}
}

foreach($D as $id=>$row) {
	$rs['C' . $id] = $C[$id];
	$rs['D' . $id] = $D[$id];
	if(isset($k[$id])) $rs['K' . $id] = $k[$id];
}

// step 1

$rs['hexSourceLength'] = strlen($hexSource);

$rs['binSource'] = '';
for($i=0; $i<strlen($hexSource); $i++) {
	$num = ord($hexSource[$i]);

	$bin = base_convert($num, 10, 2);

	if(strlen($bin) < 8) {
		while(strlen($bin) < 8) {
			$bin = 0 . $bin;
		}
	}

	$rs['binSource'] .= $bin;
}

$IP = array(
58, 50, 42, 34, 26, 18, 10, 2,
60, 52, 44, 36, 28, 20, 12, 4,
62, 54, 46, 38, 30, 22, 14, 6,
64, 56, 48, 40, 32, 24, 16, 8,
57, 49, 41, 33, 25, 17, 9, 1,
59, 51, 43, 35, 27, 19, 11, 3,
61, 53, 45, 37, 29, 21, 13, 5,
63, 55, 47, 39, 31, 23, 15, 7,
);

$IP1 = array(40,	8,	48,	16,	56,	24,	64,	32,	39,	7,	47,	15,	55,	23,	63,	31,
38,	6,	46,	14,	54,	22,	62,	30,	37,	5,	45,	13,	53,	21,	61,	29,
36,	4,	44,	12,	52,	20,	60,	28,	35,	3,	43,	11,	51,	19,	59,	27,
34,	2,	42,	10,	50,	18,	58,	26,	33,	1,	41,	9,	49,	17,	57,	25,
);

$rs['sourceIP'] = '';
foreach($IP as $IPIndex) {
	$sourceIndex = $IPIndex - 1;
	$rs['sourceIP'] .= $rs['binSource'][$sourceIndex];
}

// step 2

$L = $R = array();

$rs['L0'] = $L[0] = substr($rs['sourceIP'], 0, 32);
$rs['R0'] = $R[0] = substr($rs['sourceIP'], 32, 32);

// step 3

$E = array(32, 1, 2, 3, 4, 5,
4, 5, 6, 7, 8, 9,
8, 9, 10, 11, 12, 13,
12, 13, 14, 15, 16, 17,
16, 17, 18, 19, 20, 21,
20, 21, 22, 23, 24, 25,
24, 25, 26, 27, 28, 29,
28, 29, 30, 31, 32, 1);

$P = array(
16,	7,	20,	21,	29,	12,	28,	17,
1,	15,	23,	26,	5,	18,	31,	10,
2,	8,	24,	14,	32,	27,	3,	9,
19,	13,	30,	6,	22,	11,	4,	25,
);

for($i=1; $i<=16; $i++) {
	$rs['L' . $i] = $L[$i] = $R[$i-1];

	$rs['E(R' . ($i - 1) . ')'] = $EL = move($E, $L[$i]);

	$rs[' K' . $i] = $k[$i];
	$rs['K' . $i . ' xor E(R' . ($i - 1) . ')'] = $EL = dataXOR($EL, $k[$i]);
	$rs['S(B1)S(B2)S(B3)S(B4)S(B5)S(B6)S(B7)S(B8) ' . $i] = $s = sbox($EL);
	$rs['f = P(S(B1)S(B2)S(B3)S(B4)S(B5)S(B6)S(B7)S(B8)) ' . $i] = $f = move($P, $s);

	$rs['R' . $i] = $R[$i] = dataXOR($L[$i-1], $f);
}

$rs['L16 concat R16'] = $concat = $R[16] . $L[16];

$rs['IP1 perestanovka'] = $encoded = move($IP1, $concat);
$rs['resultat'] = '';
for($start = 0; $start < strlen($encoded); $start += 4) {
	$bin = substr($encoded, $start, 4);
	$rs['resultat'] .= strtoupper(base_convert($bin, 2, 16));
}

print_r($rs);
