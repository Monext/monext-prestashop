<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

class AdminPaylineLogsAjaxController extends ModuleAdminController
{
    /**
     * @var string
     */
    protected $pattern = '/\[(?P<date>.*)\] (?P<logger>[\w-]+).(?P<level>\w+): (?P<message>[^\[\{]+) (?P<context>[\[\{].*[\]\}]) (?P<extra>[\[\{].*[\]\}])/';

    /**
     * Process ajax query to get logs lines
     * @return void
     * @throws PrestaShopException
     * @since 2.3.6
     */
    public function ajaxProcessGetLogsLines(): void
    {
        $logFileContent = $this->getLogsLines(Tools::getValue('logfile'));
        $this->ajaxRender(
            json_encode([
                'success' => true,
                'message' => $logFileContent,
            ]));
        exit;
    }

    /**
     * Return logs file content as array
     * @since 2.3.6
     * @param $logFilename
     * @return array
     */
    protected function getLogsLines($logFilename)
    {
        $logFileContent = [];
        if ($logFilename && in_array($logFilename, $this->module->getPaylineLogsFilesList())) {
            $logFile = $this->module->getPaylineLogsDirectory() . $logFilename.'.log';
            foreach (file($logFile) as $line) {
                if (preg_match($this->pattern, trim($line), $matches)) {
                    $logFileContent[] = [
                        'date'    => $matches['date'],
                        'logger' => $matches['logger'],
                        'level'   => $matches['level'],
                        'message' => trim($matches['message']),
                        'context' => json_decode($matches['context'], true),
                        'extra'   => json_decode($matches['extra'], true),
                    ];
                }
            }
        }
        return array_reverse($logFileContent);
    }
}
