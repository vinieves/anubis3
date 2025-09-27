<?php

namespace App\Services;

class OfertaService
{
    /**
     * Obtém os dados de uma oferta específica
     */
    public static function getOferta($ofertaId)
    {
        $ofertas = config('ofertas.ofertas');
        
        // Retorna a oferta específica ou fallback para oferta1
        return $ofertas[$ofertaId] ?? $ofertas['oferta1'];
    }

    /**
     * Valida se uma oferta existe
     */
    public static function isValid($ofertaId)
    {
        $ofertasValidas = ['oferta1', 'oferta2', 'oferta3', 'oferta4'];
        return in_array($ofertaId, $ofertasValidas);
    }

    /**
     * Retorna a oferta padrão
     */
    public static function getFallback()
    {
        return self::getOferta('oferta1');
    }

    /**
     * Lista todas as ofertas disponíveis
     */
    public static function getAllOfertas()
    {
        return config('ofertas.ofertas');
    }
}
