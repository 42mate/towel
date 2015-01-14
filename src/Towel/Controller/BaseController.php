<?php

namespace Towel\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends \Towel\BaseApp
{
    public $controllerName;

    public function __construct()
    {
        parent::__construct();
        $reflection = new \ReflectionClass(get_class($this));
        $this->controllerName = strtolower($reflection->getShortName());
    }

    /**
     * Default Index page
     */
    public function index($request)
    {
        return $this->twig()->render('Default\index.twig');
    }

    /**
     * 404 and 500 error page for non debug mode
     *
     * @param \Exception $e
     *
     * @return Response
     */
    public function routeError(\Exception $e)
    {
        if ($e instanceof NotFoundHttpException) {
            $responseContent = $this->twig()->render('Default\404.twig');
            $response = new Response($responseContent, 404);
        } else {
            $responseContent = $this->twig()->render('Default\500.twig', array('error' => $e->getMessage()));
            $response = new Response($responseContent, 500);
        }

        return $response;
    }

    /**
     * Redirects the user
     *
     * @param $url
     * @param int $status : Default 302
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return $this->silex->redirect($url, $status);
    }

    /**
     * Clear all User Messages
     */
    public function sessionClearMessages()
    {
        $this->session()->set('messages', array());
    }

    /**
     * Gets user Messages
     * @return mixed
     */
    public function getMessages()
    {
        return $this->session()->get('messages', array());
    }

    /**
     * Sets a user message to present after reload or redirect.
     *
     * @param $type
     * @param $content
     */
    public function setMessage($type, $content)
    {
        $message = new \stdClass;
        $message->mt = $type;
        $message->content = $content;
        $messages = $this->session()->get('messages', array());
        $messages[] = $message;
        $this->session()->set('messages', $messages);
    }

    /**
     * Gets the Request.
     *
     * @return \Symfony\Component\HttpFoundation\Request.
     */
    public function getRequest()
    {
        return $this->app['request'];
    }

    /**
     * It will attach the array of files to the entity.
     *
     * @param $id
     * @param $table
     * @param $files
     */
    public function attachFiles($id, $table, $files)
    {
        foreach ($files as $file) {
            if (!empty($file)) {
                $this->attachFile($id, $table, $file);
            }
        }
    }

    /**
     * It will attach the file.
     *
     * @param $id
     * @param $table
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function attachFile($id, $table, \Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        $newFileName = md5(microtime() . '.' . strtolower($file->getClientOriginalExtension()));
        $relativePath = $table . '/' . date('Y/m/d');
        $relativeFilePath = $relativePath . '/' . $newFileName;
        $dstPath = APP_UPLOADS_DIR . '/' . $relativePath;
        $file->move($dstPath, $newFileName);
        $pic = new \Towel\Model\Pic();

        $pic->object_id = $id;
        $pic->object_type = $table;
        $pic->pic = $relativeFilePath;
        $pic->created = time();
        $pic->save();
    }

    /**
     * Generates a machine readable slug of the string.
     * 
     * @param $string
     */
    public function sluggify($string) {

    }
}
