<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LidRegister extends Component
{
    public $student;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.lid-register', [
            'student' => $this->student,
        ]);
    }
}