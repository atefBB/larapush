<?php

namespace Brunocfalcao\Larapush\Utilities;

use Illuminate\Support\Facades\Artisan;
use Brunocfalcao\Larapush\Concerns\CanRunProcesses;

/**
 * Class that executes scripts in the web server.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class Script
{
    use CanRunProcesses;

    public function __construct(array $scriptPayload)
    {
        $this->type = $scriptPayload[1];
        $this->command = $scriptPayload[0];
    }

    public function execute()
    {
        switch ($this->type) {
            case 'artisan':
                $error = Artisan::call($this->command);
                if ($error != 0) {
                    throw \Exception('There was an error on your Artisan command - '.Artisan::output());
                }

                return Artisan::output();
                break;

            case 'class_method':
                if (class_exists($this->command)) {
                    return (new $this->command)();
                }

                if (strpos($this->command, '@')) {
                    return app()->call($this->command);
                }
                break;

            case 'shell_cmd':
                $this->runProcess($this->command);
                break;
        }
    }
}
