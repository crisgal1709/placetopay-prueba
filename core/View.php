<?php 

namespace Core;

use Exception;

class View {

	protected $paths;
	protected $app;

    protected $shared = [];

	function __construct($app)
	{
		$this->app = $app;
	}

	public function getClosure($file, $data = [])
	{
		$closure = function () use ($file, $data) {
            // Save current buffering level so we can backtrack in case of an error.
            // This is needed because the view itself might also add an unknown number of output buffering levels.
            
        };
        return $closure;
	}

    public function shareSession(array $data = [])
    {
        if (is_null($_SESSION['view_shared'])) {
            $_SESSION['view_shared'] = [];
        }

        $d = array_merge($_SESSION['view_shared'], $data);

        $_SESSION['view_shared'] = $d;
    }

    function share(array $data = [])
    {
        $d = array_merge($this->shared, $data);
        $this->shared = $d;
    }

	public function setViewsPath($path)
	{
		$this->paths = $path;
	}

	public function render($file, array $data = [])
	{
        $share = array_merge($this->shared, $data);

        if (!is_null($_SESSION['view_shared'])) {
            $share = array_merge($share, $_SESSION['view_shared']);
        }
        $_SESSION['view_shared'] = NULL;

		extract($share, EXTR_SKIP);

		$bufferLevel = ob_get_level();
            ob_start();
            try {
                include $this->paths . '/' . $file;
            } catch (Exception $exception) {
                // Remove whatever levels were added up until now.
                while (ob_get_level() > $bufferLevel) {
                    ob_end_clean();
                }
                throw new Exception(
                    sprintf(
                        'Could not load the View file "%1$s". Reason: "%2$s".',
                        $file,
                        $exception->getMessage()
                    ),
                    $exception->getCode(),
                    $exception
                );
            }
            return ob_get_clean();
	}

}