<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class LocaleFileController extends AdminController
{

    private $lang = '';
    private $file;
    private $key;
    private $value;
    private $path;
    private $arrayLang = array();

//------------------------------------------------------------------------------
// Add or modify lang files content
//------------------------------------------------------------------------------

    public function changeLang(Request $request)
    {
// Process and prepare you data as you like.

        $this->lang = $request->cookie('lang');
        $this->file = 'menu';
        $this->key = $request['LangKey'];
        $this->value = $request['name_menu'];

// END - Process and prepare your data

        $this->changeLangFileContent();
        //$this->deleteLangFileContent();

        return 'ok';
    }

//------------------------------------------------------------------------------
// Add or modify lang files content
//------------------------------------------------------------------------------

    private function changeLangFileContent()
    {
        $this->read();
        $this->arrayLang[$this->key] = $this->value;
        $this->save();
    }

//------------------------------------------------------------------------------
// Delete from lang files
//------------------------------------------------------------------------------

    private function deleteLangFileContent()
    {
        $this->read();
        unset($this->arrayLang[$this->key]);
        $this->save();
    }

//------------------------------------------------------------------------------
// Read lang file content
//------------------------------------------------------------------------------

    private function read()
    {
        if ($this->lang == '') $this->lang = App::getLocale();
        $this->path = base_path().'/resources/lang/'.$this->lang.'/'.$this->file.'.php';
        $this->arrayLang = Lang::get($this->file);
        if (gettype($this->arrayLang) == 'string') $this->arrayLang = array();
    }

//------------------------------------------------------------------------------
// Save lang file content
//------------------------------------------------------------------------------

    private function save()
    {
        $content = "<?php\n\nreturn\n[\n";

        foreach ($this->arrayLang as $this->key => $this->value)
        {
            $content .= "\t'".$this->key."' => '".$this->value."',\n";
        }

        $content .= "];";

        file_put_contents($this->path, $content);

    }
}
