<?php

declare( strict_types = 1 );

class Sale {
    
    private string $product_code;
    private string $product_category;
    private float $product_cost;
    private int $quantity_sold;
    private float $percent_profit;
    private float $flat_profit;

    public function __construct( string $code, string $category, float $cost, int $quantity, string $sale_formula ) {
        $this->product_code = $code;
        $this->product_category = $category;
        $this->product_cost = $cost;
        $this->quantity_sold = $quantity;
        $this->get_percent_profit( $sale_formula );
        $this->get_flat_profit( $sale_formula );
    }

    public function get_category(): string {
        return $this->product_category;
    }

    public function calculate_profit(): float {
        return round( ($this->percent_profit + $this->flat_profit) * $this->quantity_sold, 2 );
    }

    private function get_percent_profit( string $formula ) {
        // Regular expression to get the percent profit from the formula
        preg_match('/[+-]*(?:\d+|\d*\.\d+)%/', $formula, $matches);
        // If the percent exists, then it must be unique
        if ( count( $matches ) === 1 ) {
            $sale_formula_percent = (float) str_replace( '%', '', $matches[0] );
            $this->percent_profit = ( $sale_formula_percent / 100 ) * $this->product_cost;
        } else {
            $this->percent_profit = 0;
        }
    }

    private function get_flat_profit( string $formula ) {
        // Regular expression to get the flat amount from the formula
        preg_match('/[+-]*(?:\d+|\d*\.\d+)€/', $formula, $matches);
        // If the flat amount exists, then it must be unique
        if ( count( $matches ) === 1 ) {            
            $this->flat_profit = (float) str_replace( '€', '', $matches[0] );
        } else {
            $this->flat_profit = 0;
        }
    }

}

?>