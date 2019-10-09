<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col"><?php echo Lang::get('games.PERIOD'); ?></th>
      <th scope="col"><?php echo Lang::get('games.ULTIMATE_PASSWORD'); ?></th>
      <th scope="col"><?php echo Lang::get('games.ROUND_ID'); ?></th>
      <th scope="col"><?php echo Lang::get('games.ROUND_AWARD'); ?></th>
      <th scope="col"><?php echo Lang::get('games.RATIO'); ?></th>
      <th scope="col"><?php echo Lang::get('games.STATUS'); ?></th>
      <th scope="col"><?php echo Lang::get('games.ENABLE_TIMESTAMP'); ?></th>
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
        $html_id = '';
        for($i = 0; $i < count($b->round_open); $i++)
        {
          $sd = ($b->round_open[$i] % 2 == 0)? Lang::get('games.DOUBLE'):Lang::get('games.SINGLE');
          $html_id .= "<p>".$b->round_id[$i]."</p>";
          $html_open .= "<p>".$b->round_open[$i]."/".$sd."</p>";
          $html_rate .= "<p>".Lang::get('games.NUMBER')."：".$b->round_ratio[$i]['n'].Lang::get('games.SINGLE')."：".$b->round_ratio[$i]['s'].Lang::get('games.DOUBLE')."：".$b->round_ratio[$i]['d']."</p>";
        }
        echo "<td>".$html_id."</td>";
        echo "<td>".$html_open."</td>";
        echo "<td>".$html_rate."</td>";

        
        echo "<td>".$b->is_close."</td>";
        echo "<td>".$b->created_at."</td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>