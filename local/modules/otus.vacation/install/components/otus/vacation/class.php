<?php
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;

class VacationComponent
    extends \CBitrixComponent
    implements Errorable
{
    /** @var ErrorCollection */
    protected $errorCollection;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * Return true if collection has errors.
     *
     * @return boolean
     */
    public function hasErrors()
    {
        if ($this->errorCollection instanceof ErrorCollection) {
            return !$this->errorCollection->isEmpty();
        }

        return false;
    }

    /**
     * Getting array of errors.
     *
     * @return Error[]
     */
    public function getErrors()
    {
        if ($this->errorCollection instanceof ErrorCollection) {
            return $this->errorCollection->toArray();
        }

        return [];
    }

    /**
     * Returns an error with the necessary code.
     *
     * @param string|int $code The code of the error.
     *
     * @return Error|null
     */
    public function getErrorByCode($code)
    {
        if ($this->errorCollection instanceof ErrorCollection) {
            return $this->errorCollection->getErrorByCode($code);
        }

        return null;
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams = parent::onPrepareComponentParams($arParams);

        if ($arParams['SEF_MODE'] != 'Y') {
            $this->errorCollection->setError(
                new Main\Error('Only SEF mode')
            );
        }


        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->hasErrors()) {
            ShowError($this->getErrors());
            return false;
        }

        $arResult = &$this->arResult;
        $arParams = &$this->arParams;

        $arDefaultUrlTemplates404 = $arParams['SEF_URL_TEMPLATES'];

        $arDefaultVariableAliases404 = [];

        $arComponentVariables = ['code'];

        $arVariables = [];

        $arUrlTemplates = \CComponentEngine::MakeComponentUrlTemplates(
            $arDefaultUrlTemplates404,
            $arParams['SEF_URL_TEMPLATES']
        );

        $arVariableAliases = \CComponentEngine::MakeComponentVariableAliases(
            $arDefaultVariableAliases404,
            $arParams['VARIABLE_ALIASES']
        );

        $componentPage = \CComponentEngine::ParseComponentPath(
            $arParams['SEF_FOLDER'],
            $arUrlTemplates,
            $arVariables
        );

        if (!(is_string($componentPage)
            && isset($componentPage[0])
            && isset($arDefaultUrlTemplates404[$componentPage]))
        ) {
            $componentPage = 'vacation_grid';
        }

        \CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        foreach ($arUrlTemplates as $url => $value) {
            $key = 'PATH_TO_' . strtoupper($url);
            $arResult[$key] = isset($arParams[$key][0]) ? $arParams[$key] : $arParams['SEF_FOLDER'] . $value;
        }

        $arResult =
            array_merge([
                'VARIABLES' => $arVariables,
            ],
                $arResult
            );

        $arResult['TEMPLATE_PATH_LIST'] = $this->getTemplatePathList($arParams['SEF_FOLDER'], $arParams['SEF_URL_TEMPLATES']);

        $this->IncludeComponentTemplate($componentPage);
    }

    /**
     * Получение абсолютных шаблонов URL
     * @param string $folder
     * @param array $urlTemplates
     * @return array
     */
    private function getTemplatePathList(string $folder, array $urlTemplates = []): array
    {
        return array_map(static function ($e) use ($folder) {
            return str_replace('//', '/', $folder . $e);
        }, $urlTemplates);
    }
}
