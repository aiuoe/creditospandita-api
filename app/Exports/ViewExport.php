<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ViewExport implements FromView
{
	public $params;
	public function __construct( $params ) {
       $this->params = $params;
    }

    public function view(): View
    {
        return view($this->params['view'], $this->params);
    }
}
?>
