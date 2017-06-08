<?php
namespace sf\web;

/**
 * Controller is the base class for classes containing controller logic.
 * @author Harry Sun <sunguangjun@126.com>
 */
class Controller extends \sf\base\Controller
{
    /**
     * Renders a view
     * @param string $view the view name.
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     */
    public function render($view, $params = [])
    {
        $file = '../views/' . $view . '.sf';
        $fileContent = file_get_contents($file);
        $result = '';
        foreach (token_get_all($fileContent) as $token) {
            if (is_array($token)) {
                list($id, $content) = $token;
                if ($id == T_INLINE_HTML) {
                    $content = preg_replace('/{{(.*)}}/', '<?php echo $1 ?>', $content);
                }
                $result .= $content;
            } else {
                $result .= $token;
            }
        }
        $generatedFile = '../runtime/cache/' . md5($file);
        file_put_contents($generatedFile, $result);
        extract($params);
        require_once $generatedFile;
    }

    /**
     * Convert a array to json string
     * @param string $data
     */
    public function toJson($data)
    {
        if (is_string($data)) {
            return $data;
        }
        return json_encode($data);
    }
}