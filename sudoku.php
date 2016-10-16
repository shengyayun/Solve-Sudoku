<html>
<head>
    <title>Sudoku</title>
    <style type="text/css">table{margin:0 auto;}td{border:1px solid #515151;padding:10px 20px;}.odd{background:#ffffff}.even{background:#CDCDC1}.blue{color:blue}body{text-align:center;padding:20px}input{text-align: center;padding: 5px 10px;margin-right:10px}</style>
</head>
<body>
    <h1>Sudoku</h1>
<?php
set_time_limit(0);
class Sudoku
{
    //题目提供的值
    public $default = array();
    //每个元素所在的九宫号
    public $class = array();
    //九宫号与元素xy坐标的映射
    public $nonet = array();
    //结果
    public $result = false;

    public function __construct($subject)
    {
        $numbers = str_split($subject);
        $nonetHolder = array();
        $this->result = $this->default;
        for ($i = 0; $i < 9; $i++)
        { 
            $this->class[$i] = array();
            $this->default[$i] = array();
            for($j = 0; $j < 9; $j++)
            {
                $this->default[$i][$j] = intval(array_shift($numbers));
                $nonetHolder[($i % 3) . '/' . ($j % 3)][] = array($i, $j);
            }
        }
        for ($i = 0; $i < 9; $i++)
        {
            foreach ($nonetHolder as $key => $value) 
            {
                $x = $value[$i][0];
                $y = $value[$i][1];
                $this->class[$x][$y] = $i;
                $this->nonet[$i][] = [$x, $y];
            }
        }
    }

    /**
     * 输出到html
     * @param  array $list 结果数组
     * @param  list  $span 运算花费时间
     * @return void
     */
    public function out($list, $span)
    {
        if(empty($list)) $list = $this->default;
        echo "<p>$span seconds</p>";
        echo '<table>';
        for ($i = 0; $i < 9; $i++)
        { 
            echo "<tr>";
            for($j = 0; $j < 9; $j++)
            {
                $value = $list[$i][$j] == 0 ? null : $list[$i][$j];
                //是否题目中已经提供的
                $default = $this->default[$i][$j] > 0;
                //九宫号
                $class = $this->class[$i][$j];

                echo "<td class='" . ($class % 2 ? 'odd' : 'even') . ($default ? '' : ' blue') . "''>";
                echo $value;
                echo "</td>";
            }
            echo "</tr>";
        }
        echo '</table>';
    }

    public function exhaustive($list, $cursor, $len = 81)
    {
        if($cursor < $len)
        {
            list($x, $y) = $this->index2xy($cursor);
            if($list[$x][$y] == 0)
            {
                //0 => 0, 1 => 1 ...
                //该格子允许的值
                $can = [0, 1, 2, 3 ,4 ,5 ,6 ,7, 8, 9,];
                for ($i = 0; $i < 9; $i++)
                {
                    //x轴上已经被用掉的unset掉
                    unset($can[$list[$x][$i]]);
                    //y轴上已经被用掉的unset掉
                    unset($can[$list[$i][$y]]);
                }
                //同一宫内已经被用掉的unset掉
                foreach ($this->nonet[$this->class[$x][$y]] as $value) 
                {
                    unset($can[$list[$value[0]][$value[1]]]);
                }
            }
            else
            {
                $can = [$list[$x][$y]];
            }
            //对每个可能值循环递归
            foreach ($can as $value)
            {
                $list[$x][$y] = $value;
                $this->exhaustive($list, $cursor + 1, $len);
            }
        }
        else
        {
            $this->result = $list;
        }
    }

    /**
     * 从到到右从上到下的索引转换为xy坐标
     * @param  int $index
     * @return array
     */
    public function index2xy($index)
    {
        $x = intval($index / 9);
        $y = $index % 9;
        return [$x, $y];
    }

    /**
     * xy坐标转换为索引
     * @param  int $x
     * @param  int $y
     * @return int
     */
    public function xy2index($x, $y)
    {
        return $x * 9 + $y;
    }

    /**
     * main
     * @return void
     */
    public function main()
    {
        $st = microtime(true);
        $this->exhaustive($this->default, 0);
        $span = microtime(true) - $st;
        $this->out($this->result, $span);
    }
}

if(array_key_exists('code', $_GET))
{
    $code = trim($_GET['code']);
    if(!preg_match("/\d{81,81}/", $code))
    {
        echo "<script>alert('code invalid');location.href='/sudoku.php'</script>";
        die;
    }
    $sudoku = new Sudoku($_GET['code']);
    $sudoku->main();
}
else
{
    ?>
    <form method='get'>
            <input type='text' name='code' maxlength='81' size='100' value='000080090000000053614000000127030006940207080085964000451600809060071534873095102'/>
            <input type='submit' value='Submit'/>
    </form>
    <?php
}
    ?>
</body>
</html>
