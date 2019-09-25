<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">期数</th>
      <th scope="col">终极密码</th>
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
        echo "<td>".$b->openWin."</td>";
        echo "<td>".$b->isClose."</td>";
        echo "<td>".$b->created_at."</td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>