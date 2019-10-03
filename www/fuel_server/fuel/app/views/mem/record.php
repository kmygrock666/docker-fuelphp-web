<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col"><?php echo Lang::get('games.BET_ID'); ?></th>
      <th scope="col"><?php echo Lang::get('games.ROUND_ID'); ?></th>
      <th scope="col"><?php echo Lang::get('games.PERIOD'); ?></th>
      <th scope="col"><?php echo Lang::get('games.GAME_TYPE'); ?></th>
      <th scope="col"><?php echo Lang::get('games.BET_DATA'); ?></th>
      <th scope="col"><?php echo Lang::get('games.BET_AMOUNT'); ?></th>
      <th scope="col"><?php echo Lang::get('games.WIN_AMOUNT'); ?></th>
      <th scope="col"><?php echo Lang::get('games.STATUS'); ?></th>
      <th scope="col"><?php echo Lang::get('games.BET_TIMESTAMP'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($betdata as $b)
      {
        echo "<tr>";
        echo "<th scope='row'>".$b->id."</th>";
        echo "<th scope='row'>".$b->round_id."</th>";
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