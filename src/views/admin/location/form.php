<?php echo $view['form']->start($form) ?>
    <?php echo $view['form']->errors($form) ?>

    <?php echo $view['form']->row($form['task']) ?>
    <?php echo $view['form']->row($form['dueDate']) ?>
<?php echo $view['form']->end($form) ?>
