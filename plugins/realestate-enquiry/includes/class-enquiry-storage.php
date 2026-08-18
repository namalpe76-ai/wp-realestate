<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Ree_Enquiry_Storage {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'realestate_enquiries';
    }

    public function insert( $data ) {
        global $wpdb;

        $insert_data = array(
            'full_name'      => $data['full_name'],
            'email'          => $data['email'],
            'telephone'      => $data['telephone'],
            'contact_method' => $data['contact_method'],
            'property_id'    => $data['property_id'],
            'property_name'  => $data['property_name'],
            'enquiry_type'   => $data['enquiry_type'],
            'viewing_date'   => $data['viewing_date'],
            'message'        => $data['message'],
            'status'         => $data['status'],
            'created_at'     => current_time( 'mysql' ),
            'updated_at'     => current_time( 'mysql' ),
        );

        $format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );

        $result = $wpdb->insert( $this->table_name, $insert_data, $format );

        if ( false === $result ) {
            return new WP_Error( 'db_insert_error', __( 'Could not insert enquiry.', 'realestate-enquiry' ), array( 'status' => 500 ) );
        }

        return $wpdb->insert_id;
    }

    public function update_status( $id, $status ) {
        global $wpdb;

        $valid_statuses = array( 'new', 'contacted', 'viewing_scheduled', 'negotiation', 'closed' );
        if ( ! in_array( $status, $valid_statuses, true ) ) {
            return new WP_Error( 'invalid_status', __( 'Invalid status value.', 'realestate-enquiry' ) );
        }

        $result = $wpdb->update(
            $this->table_name,
            array(
                'status'     => $status,
                'updated_at' => current_time( 'mysql' ),
            ),
            array( 'id' => absint( $id ) ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_update_error', __( 'Could not update enquiry status.', 'realestate-enquiry' ) );
        }

        return true;
    }

    public function delete( $id ) {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            array( 'id' => absint( $id ) ),
            array( '%d' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_delete_error', __( 'Could not delete enquiry.', 'realestate-enquiry' ) );
        }

        return true;
    }

    public function get( $id ) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            absint( $id )
        );

        return $wpdb->get_row( $query );
    }

    public function get_all( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page'     => 1,
            'orderby'  => 'created_at',
            'order'    => 'DESC',
            'status'   => '',
            'search'   => '',
        );

        $args  = wp_parse_args( $args, $defaults );
        $where = array();
        $values = array();

        if ( ! empty( $args['status'] ) ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }

        if ( ! empty( $args['search'] ) ) {
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[]     = '(full_name LIKE %s OR email LIKE %s OR property_id LIKE %s OR property_name LIKE %s)';
            $values[]    = $search_term;
            $values[]    = $search_term;
            $values[]    = $search_term;
            $values[]    = $search_term;
        }

        $where_clause = '';
        if ( ! empty( $where ) ) {
            $where_clause = 'WHERE ' . implode( ' AND ', $where );
        }

        $allowed_orderby = array( 'id', 'full_name', 'email', 'created_at', 'status', 'enquiry_type' );
        $orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
        $order           = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
        $per_page        = absint( $args['per_page'] );
        $page            = absint( $args['page'] );
        $offset          = ( $page - 1 ) * $per_page;

        $count_query = "SELECT COUNT(*) FROM {$this->table_name} {$where_clause}";
        $total       = empty( $values )
            ? $wpdb->get_var( $count_query )
            : $wpdb->get_var( $wpdb->prepare( $count_query, ...$values ) );

        $data_query = "SELECT * FROM {$this->table_name} {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        $data_vals  = $values;
        $data_vals[] = $per_page;
        $data_vals[] = $offset;

        $items = $wpdb->get_results( $wpdb->prepare( $data_query, ...$data_vals ) );

        return array(
            'items'       => $items ? $items : array(),
            'total'       => (int) $total,
            'per_page'    => $per_page,
            'total_pages' => (int) ceil( $total / $per_page ),
            'page'        => $page,
        );
    }

    public function export_csv( $args = array() ) {
        $args['per_page'] = 0;
        $result           = $this->get_all( $args );

        $csv_data = array();

        $csv_data[] = array(
            __( 'ID', 'realestate-enquiry' ),
            __( 'Full Name', 'realestate-enquiry' ),
            __( 'Email', 'realestate-enquiry' ),
            __( 'Telephone', 'realestate-enquiry' ),
            __( 'Contact Method', 'realestate-enquiry' ),
            __( 'Property ID', 'realestate-enquiry' ),
            __( 'Property Name', 'realestate-enquiry' ),
            __( 'Enquiry Type', 'realestate-enquiry' ),
            __( 'Viewing Date', 'realestate-enquiry' ),
            __( 'Message', 'realestate-enquiry' ),
            __( 'Status', 'realestate-enquiry' ),
            __( 'Created At', 'realestate-enquiry' ),
            __( 'Updated At', 'realestate-enquiry' ),
        );

        foreach ( $result['items'] as $row ) {
            $csv_data[] = array(
                $row->id,
                $row->full_name,
                $row->email,
                $row->telephone,
                $row->contact_method,
                $row->property_id,
                $row->property_name,
                $row->enquiry_type,
                $row->viewing_date,
                $row->message,
                $row->status,
                $row->created_at,
                $row->updated_at,
            );
        }

        return $csv_data;
    }

    public function count_by_status( $status ) {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE status = %s",
                $status
            )
        );
    }

    public function get_total_count() {
        global $wpdb;

        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );
    }
}
