<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">类型</th>
      <th scope="col">备注</th>
      <th scope="col">原始余额</th>
      <th scope="col">金额</th>
      <th scope="col">变动后余额</th>
      <th scope="col">交易时间</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($dealdata as $b)
      {
        echo "<tr>";
        echo "<th scope='row'>".$b->type."</th>";
        echo "<td>".$b->remark."</td>";
        echo "<td>".$b->before_amount."</td>";
        echo "<td>".$b->amount."</td>";
        echo "<td>".$b->after_amount."</td>";
        echo "<td>".$b->created_at."</td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>