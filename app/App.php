<?php

declare( strict_types = 1 );

class App {

    private array $sales_formulas;
    private array $profit_by_category = [];

    public function __construct( string $csv_file, string $json_file ) {
        $this->sales_formulas = $this->import_sales_formulas( $json_file );
        $this->profit_by_category = $this->calculate_profit_by_category( $csv_file );
    }

    public function get_profit_by_category() {        
        foreach ( $this->profit_by_category as $key => $value ) {
            $value = number_format( $value, 2, '.', '' );
            echo "{$key}: {$value}\n";
        }
    }

    private function import_sales_formulas( string $file_name ): array {
	    // Trigger an error if we can't find the file
    	if ( ! file_exists( $file_name ) ) {
    		trigger_error( 'File "' . $file_name . '" does not exist.', E_USER_ERROR );
    	}
    	// If we can find the file, we get its content to decode the JSON file
    	$json = file_get_contents( $file_name );
    	return json_decode( $json, true );
    }

    private function calculate_profit_by_category( string $file_name ): array {
        // Trigger an error if we can't find the file
        if ( ! file_exists( $file_name ) ) {
            trigger_error( 'File "' . $file_name . '" does not exist.', E_USER_ERROR );
        }
        // If we can find the file, we'll open it in read-only mode
        $file = fopen( $file_name, 'r' );
        // The header of the CSV file will determine the order of the columns
    	$csv_header = explode( ";", strtolower( implode( fgetcsv( $file ) ) ) );
        // We read the CSV file line by line, adding its content to our sale array
        while ( ( $csv_line = fgetcsv( $file, null, ";" ) ) !== false ) {
    		$sale_line = [];
    		foreach( $csv_line as $field_position => $field_name ) {
    			$sale_line[$csv_header[$field_position]] = $field_name;
            }
            // Clean the "cost" column
            $sale_line['cost'] = str_replace( ['€', '.'], '', $sale_line['cost'] );
            $sale_line['cost'] = (float) str_replace( ',', '.', $sale_line['cost'] );
            // Clean the "quantity" column
            $sale_line['quantity'] = (int) str_replace( '.', '', $sale_line['quantity'] );
            // Add the "sales_formula" column
            if ( ! isset( $this->sales_formulas['categories'][$sale_line['category']] ) ) {
                $sale_line['sale_formula'] = $this->sales_formulas['categories']['*'];
            } else {
                $sale_line['sale_formula'] = $this->sales_formulas['categories'][$sale_line['category']];
            }
            // Create a "Sale" object
            $sale = new Sale( $sale_line['product'], $sale_line['category'], $sale_line['cost'], $sale_line['quantity'], $sale_line['sale_formula'] );
            // Add the profit to it's product category
            $this->add_sale_profit_to_category( $sale );
        }
        fclose( $file );
        return $this->profit_by_category;
    }

    private function add_sale_profit_to_category( Sale $sale ) {
        // If the category exists, accumulate profit
        if ( isset( $this->profit_by_category[$sale->get_category()] ) ) {
            $this->profit_by_category[$sale->get_category()] += $sale->calculate_profit();
        } else {
            $this->profit_by_category[$sale->get_category()] = $sale->calculate_profit();
        }
    }

}

?>