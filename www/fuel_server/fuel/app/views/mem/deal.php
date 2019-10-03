<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col"><?php echo Lang::get('games.GAME_TYPE'); ?></th>
      <th scope="col"><?php echo Lang::get('games.REMARK'); ?></th>
      <th scope="col"><?php echo Lang::get('games.ORIGINAL_BALANCE'); ?></th>
      <th scope="col"><?php echo Lang::get('games.AMOUNT'); ?></th>
      <th scope="col"><?php echo Lang::get('games.AFTER_BALANCE'); ?></th>
      <th scope="col"><?php echo Lang::get('games.DEAL_TIMESTAMP'); ?></th>
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