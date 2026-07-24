<?php

namespace App\Enums;

enum TipoArchivoClinico: string
{
    case INCISIVO = 'Incisivo';
    case CANINO = 'Canino';
    case PREMOLAR = 'Premolar';
    case MOLAR = 'Molar';
}
