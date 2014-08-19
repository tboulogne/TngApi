<head>
  <!-- The title goes in the head -->
  <title><?php echo $title; ?></title>
</head>
<body>
  <!-- The message and back link go somewhere in the body -->
  <ul>
    <?php echo $message; ?>
  </ul>
  <?php
    // Do we need a back button?
    if( isset( $back_link ) )
    {
      echo '<p>' . $back_link . '</p>';
    }
  ?>
</body>