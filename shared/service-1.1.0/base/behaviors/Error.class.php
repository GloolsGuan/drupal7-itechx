<?php
namespace service\base\behaviors;


class Error extends \yii\base\Behavior
{
    const PARTICIPANT_ALLERADY_EXISTS_IN_GROUP = 202;
    const PARTICIPANT_IS_NOT_TRAINEE = 203;
    const UPLOAD_FILE_FAILED = 204;
    const LEAF_DOES_NOT_EXIST = 205;
    const SIGN_IS_NOT_FOUND = 206;
    const TASK_DOES_NOT_FOUND = 207;
    const INVALID_PARAMETER = 240;
    const FAILED = 250;
    
    
     /**
     * @var array validation errors (category name => array of errors)
     */
    private $_errors;
    
    public $auto_log = false;
    public $log_level = 'exception';
    
    /**
     * Returns the errors for all category or a single category.
     * @param string $category category name. Use null to retrieve errors for all categories.
     * @property array An array of errors for all categories. Empty array is returned if no error.
     * The result is a two-dimensional array. See [[getErrors()]] for detailed description.
     * @return array errors for all categories or the specified category. Empty array is returned if no error.
     * Note that when returning errors for all categories, the result is a two-dimensional array, like the following:
     *
     * ~~~
     * [
     *     'username' => [
     *         'Username is required.',
     *         'Username must contain only word characters.',
     *     ],
     *     'email' => [
     *         'Email address is invalid.',
     *     ]
     * ]
     * ~~~
     *
     * @see getFirstErrors()
     * @see getFirstError()
     */
    public function getErrors($category = null)
    {
        if ($category === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$category]) ? $this->_errors[$category] : [];
        }
    }
    
    /**
     * Returns the first error of the specified category.
     * @param string $category category name.
     * @return string the error message. Null is returned if no error.
     * @see getErrors()
     * @see getFirstErrors()
     */
    public function getFirstError($category)
    {
        return isset($this->_errors[$category]) ? reset($this->_errors[$category]) : null;
    }

    /**
     * Adds a new error to the specified category.
     * @param string $category category name
     * @param string $error new error message
     */
    public function addError($category, $error = '', $level='notice')
    {
        $this->_errors[$category][] = sprintf('(%s) %s.', $level, $error);
    }

    /**
     * Adds a list of errors.
     * @param array $items a list of errors. The array keys must be category names.
     * The array values should be error messages. If an category has multiple errors,
     * these errors must be given in terms of an array.
     * You may use the result of [[getErrors()]] as the value for this parameter.
     * @since 2.0.2
     */
    public function addErrors(array $items)
    {
        foreach ($items as $category => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $this->addError($category, $error);
                }
            } else {
                $this->addError($category, $errors);
            }
        }
    }

    /**
     * Removes errors for all categories or a single category.
     * @param string $category category name. Use null to remove errors for all category.
     */
    public function clearErrors($category = null)
    {
        if ($category === null) {
            $this->_errors = [];
        } else {
            unset($this->_errors[$category]);
        }
    }
    
    
    /**
     * Returns a value indicating whether there is any validation error.
     * @param string|null $category category name. Use null to check all categories.
     * @return boolean whether there is any error.
     */
    public function hasErrors($category = null)
    {
        return $category === null ? !empty($this->_errors) : isset($this->_errors[$category]);
    }
}

