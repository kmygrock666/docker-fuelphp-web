<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">期数</th>
      <th scope="col">终极密码</th>
      <th scope="col">回合獎號</th>
      <th scope="col">賠率</th>
      <th scope="col">状态</th>
      <th scope="col">开盘时间</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($pdata as $b)
      {
        echo "<tr>";
        echo "<th scope='row'>".$b->pid."</th>";
        echo "<td>".$b->open_win."</td>";
        $html_open = '';
        $html_rate = '';
        for($i = 0; $i < count($b->round_open); $i++)
        {
          $sd = ($b->round_open[$i] % 2 == 0)? '單':'雙';
          $html_open .= "<p>".$sd."</p>";
          $html_rate .= "<p>號碼：".$b->round_ratio[$i]['n']."/單：".$b->round_ratio[$i]['s']."/雙：".$b->round_ratio[$i]['d']."</p>";
        }
        echo "<td>".$html_open."</td>";
        echo "<td>".$html_rate."</td>";

        
        echo "<td>".$b->is_close."</td>";
        echo "<td>".$b->created_at."</td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>