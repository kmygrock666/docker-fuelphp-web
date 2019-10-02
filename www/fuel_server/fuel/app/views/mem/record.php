<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">单号</th>
      <th scope="col">期數</th>
      <th scope="col">类型</th>
      <th scope="col">下注号码</th>
      <th scope="col">下注金额</th>
      <th scope="col">中奖金额</th>
      <th scope="col">状态</th>
      <th scope="col">下注时间</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($betdata as $b)
      {
        echo "<tr>";
        echo "<th scope='row'>".$b->id."</th>";
        echo "<th scope='row'>".$b->period_id."</th>";
        echo "<td>".$b->type."</td>";
        echo "<td>".$b->bet_number."</td>";
        echo "<td>".$b->amount."</td>";
        echo "<td>".$b->payout."</td>";
        echo "<td>".$b->status."</td>";
        echo "<td>".$b->created_at."</td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>