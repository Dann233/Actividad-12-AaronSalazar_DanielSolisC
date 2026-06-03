<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pendiente  = 'pendiente';
    case EnProceso  = 'en_proceso';
    case Finalizado = 'finalizado';
}
