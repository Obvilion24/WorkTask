<?php
    echo "Hello world" ;
    echo "<br>";
    $name = 'Thanh Trung  vu tru' ;
    echo "$name";
    echo "<br>";
    var_dump ($name);
    echo "<br>";
    var_dump ((int)$name);
    echo "<br>";
    $fruit = array ("apple","tomato","mango");
    foreach ($fruit as $A)
        echo "$A <br>" ;

    // hàm tư định nghĩa
    function fcname ($name, $age ){
        echo "Hello $name. Hiện tại  $age tuổi. <br>";
    }
    // gọi hàm 
    fcname ("Thanh",21);
    // vẽ bảng 
    echo "< table border =1 >";
    echo "<tr>";
    echo "<th> Text heading </th>";
    echo "<td> text here </td>";
    echo"</tr>";
    echo "</table>";
    echo "<br>";
    function Drawtable ($row,$col){
        echo "<table border = 1 style='color:blue'>";
            for ($r=1;$r<=$row;$r++){
                echo "<tr>";
                    for ($c=1;$c<=$col;$c++){
                        echo "<td>Trunghehe </td>";
                    }
                    echo "</tr>";
            }
        echo"</table>";
    }
    Drawtable(4,4);// gọi hàm k có gtri trả về 
	// Hàm k có gtri trả về 
    function fname (string $name){
        echo $name;
    }
    // Gọi hàm k có gtri trả về 
    fname ("Thanh Tuấn <br>");

    //Hàm có gtri trả về 
    function fname1 (string $name){
        return $name;
    }
    //gọi hàm có gtri trả về 
    //c1:
    $n = fname1("Thanh <br>");
    echo $n;
    //c2:
    echo fname1 ("Trung <br>");

    // Tham số hàm là tham chiếu 
    function swap(int &$x, int &$y){
        $c = $x;
        $x=$y;
        $y=$c;
    }
    $a=6;
    $b=7;
    swap($a,$b);
    echo("Sau khi hoán vị: ");
    echo $a;
    echo $b; 
    echo "<br>";
    // mảng
    $cars = array("Toyota","Volvo","Mitsu");
    var_dump ($cars);
    echo "<br>";
    echo count($cars);
    $car[1]="Honda";
    var_dump ($cars);
    echo "<br>";
    array_push($cars, "Maybach","BMW");
    echo "<br>";
    var_dump ($cars);
    echo "<br>";
    // xóa phần tử 
    array_splice($cars,1,1);
    echo "<br>";
    var_dump ($cars);
    echo "<br>";
    // sắp xếp 
    sort ($cars);
    var_dump ($cars);
    echo "<br>";

    //mảng 2 chiều 
    $cars =array (
        array("toyota",5,"500m"),
        array("BMW",4,"5000m"),
        array("Honda",5,"25m"),
    );
    var_dump ($cars);
    echo "<br>";
    for ($i = 0;$i<3;$i++){
        for ($j=0;$j<3;$j++){
            echo $cars [$i][$j]."  |  ";
        }
    }
    echo "<br>";

    // super global variable
    //$GLOBALS
    //$_SERVER
    //$_POST
    //$_GET
    //$_ENV
    //$_COOKIE
    //$_SESSION/
    // test
   $x = 15;

    // The function is defined as "printX"
    function printX() {
        global $x;
        echo "Inside function: " . $x . "<br>";
    }

    // The call must match the definition exactly
    printX(); 
    function printServer(){
        echo "server ".$_SERVER['PHP_SELF']."<br>";
        echo "server name ".$_SERVER['SERVER_NAME']."<br>";
        echo "server host".$_SERVER['HTTP_HOST']."<br>";
        echo "server script".$_SERVER['SCRIPT_NAME']."<br>";
	}

// mở file
// đọc file
// vờ ktra đọc đến cuối file chưa feof
// fgets đọc 1 dòng
//fgetc đọc từng ký tự
// fwrite để ghi văn bản vào file
// một số mode đọc ghi: r: read, w: ghi, a: append

$file = fopen ('DATA.txt','r');
while (!feof ($file)){
    echo fgets($file);
    echo'<br>';
}

?>
