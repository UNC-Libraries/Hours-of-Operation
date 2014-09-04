<?php

namespace Hoo;

class View implements \ArrayAccess
{
  
  private $views_path;
  /**
   * View file to include
   * @var string
   */
  private $file;

  /**
   * View data
   * @var array
   */
  private $data;

  /**
   * Layout to include (optional)
   * @var string
   */
  private $layout;
  
  /**
   * Constructor
   *
   * @param string $file file to include
   */
  public function __construct($file)
  {
    $this->file = $file;
    $this->views_path = HOO__PLUGIN_DIR . 'src/views/';

  }

  /**
   * render Renders the view using the given data
   *
   * Example:
   *
   * <code>
   *  $v = new View('view.php');
   *  $v->render(array('title' => 'My view'));
   * </code>
   *
   * @param array $data
   * @return void>
   */
  public function render( $data = null )
  {
    $this->data = ( null === $data ) ? array() : $data;
    $this->layout = null;

    ob_start();

    include( $this->views_path . $this->file . '.php' );

    // did we set a layout?
    if ( null === $this->layout )
    {
      ob_end_flush();
    }
    else
    {
      ob_end_clean();
      $this->include_file($this->layout);
    }
  }

  /**
   * fetch Fetches the view result intead of sending it to the output buffer
   *
   * Example:
   *
   * <code>
   *  $v = new View('view.php');
   *  $content = $v->fetch(array('title' => 'My view'));
   * </code>
   *
   * @param array $data
   * @return string The rendered view content
   */
  public function fetch( $data = null )
  {
    ob_start();
    $this->render( $data );
    return ob_get_clean();
  }

  /**
   * get_data Returns the view data
   *
   * Example:
   *
   * run.php
   * <code>
   *  $v = new View('view.php');
   *  $v->render();
   *  $data = $v->get_data();
   *  echo $data['response'];
   * </code>
   *
   * view.php
   * <code>
   *  <?php $this['response'] = 'Hello' ?>
   * </code>
   *
   * @return array
   */
  public function get_data()
  {
    return $this->data;
  }

  /**
   * include_file Used by view to include sub-views
   *
   * Example:
   *
   * view.php
   * <code>
   *  <html>
   *  <body>
   *    body content
   *    <?php $this->include_file('footer.php') ?>
   *  </body>
   *  </html>
   * </code>
   *
   * @param string $file
   * @return void
   */
  protected function include_file($file)
  {
    $v = new View($file);
    $v->render($this->data);
    $this->data = $v->get_data();
  }

  /**
   * set_layout Used by view to indicate the use of a layout.
   *
   * If a layout is selected, the normal output of the view wil be
   * discarded.  The only way to send data to the layout is via
   * capture()
   *
   * Example:
   *
   * main_view.php
   * <code>
   *  <?php $this->set_layout('layout.php') ?>
   *  <?php $this->capture() ?>
   *    body content
   *  <?php $this->end_capture('body') ?>
   * </code>
   *
   * layout.php
   * <code>
   *  <html>
   *  <body>
   *    <?php echo $this['body'] ?>
   *    <?php $this->include_file('footer.php') ?>
   *  </body>
   *  </html>
   * </code>
   *
   * @param string $file
   * @return void
   */
  protected function set_layout($file)
  {
    $this->layout = $file;
  }

  /**
   * capture Used by view to capture output.
   *
   * When a view is using a layout (via set_layout()), the only way to pass
   * data to the layout is via capture(), but the view can use capture()
   * to capture text any time, for any reason, even if the view is not using
   * a layout
   *
   * Example:
   *
   * run.php
   * <code>
   *  $v = new View('view.php');
   *  $v->render();
   *  $data = $v->get_data();
   *  echo $data['response'];
   * </code>
   *
   * view.php
   * <code>
   *  <?php $this->capture() ?>
   *    captured content
   *  <?php $this->end_capture('response') ?>
   * </code>
   *
   * @return void
   */
  protected function capture()
  {
    ob_start();
  }

  /**
   * end_capture Used by view to signal end of a capture().
   *
   * The content of the capture is stored under $name
   *
   * Example:
   *
   * run.php
   * <code>
   *  $v = new View('view.php');
   *  $v->render();
   *  $data = $v->get_data();
   *  echo $data['response'];
   * </code>
   *
   * view.php
   * <code>
   *  <?php $this->capture() ?>
   *    captured content
   *  <?php $this->end_capture('response') ?>
   * </code>
   *
   * @param string $name
   * @return void
   */
  protected function end_capture($name)
  {
    $this->data[$name] = ob_get_clean();
  }

  /*
   * ArrayAccess methods
   *
   * Examples:
   *
   * view.php
   * <code>
   *  <?php echo $this['title'] ?>
   *  <?php $this['foo'] = 'bar' ?>
   * </code>
   */
  public function offsetExists($offset)      { return isset($this->data[$offset]); }
  public function offsetGet($offset)         { return $this->data[$offset]; }
  public function offsetSet($offset, $value) { $this->data[$offset] = $value; }
  public function offsetUnset($offset)       { unset($this->data[$offset]); }

}

?>
