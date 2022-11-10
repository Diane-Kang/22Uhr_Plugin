<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * PE_Firmengruppe_import Class Doc Comment
 */

// Dataform 
// 'Buchungskreis' => string '85' (length=2)
// 'Marken Name Kurz' => string 'G.U.T.' (length=6)
// 'Standortname' => string 'G.U.T. Glaser KG' (length=16)
// 'Slug' => string 'gut-glaser-kg' (length=13)
// 'Logo Filename' => string 'gut-glaser-kg-logo.svg' (length=22)
// 'Straße' => string 'Harpener Feld' (length=13)
// 'Hausnummer' => string '8' (length=1)
// 'PLZ' => string '44805' (length=5)
// 'Stadt' => string 'Bochum' (length=6)
// 'Land Name' => string 'Germany' (length=7)
// 'Standorttyp Name' => string 'HAUPTHAUS' (length=9)
// 'Homepage' => string 'https://www.gut-gruppe.de/de/unternehmen/glaser/locations/haupthaus-bochum' (length=74)
// 'Breitengrad' => string '51,499,803,321,114,100' (length=22)
// 'Längengrad' => string '51,499,803,321,114,100' (length=22)
// 'Bundesland' => string 'NRW' (length=3)
// 'Werblicher Anzeige Name' => string 'HAUPTHAUS Bochum' (length=16)
// 'Breitengrad (Komma)' => string '51.49980332' (length=11)
// 'Längengrad (Komma)' => string '7.261736356' (length=11)
// 'Statement' => string 'Es ist mir eine Herzensangelegenheit, alles mir mögliche zu tun, unseren Kindern eine grüne und lebenswerte Erde zu hinterlassen. An diesem Projekt kann wirklich jeder mit kleinen Maßnahmen mitwirken und GUTes tun, auch für den Tierschutz.' (length=243)
// 'Statementgeber' => string 'Stefan' (length=6)
// 'Funktion des Statementgebers' => string 'PhG' (length=3)
// 'Um wie viel Uhr wird das Licht ausgestellt?' => string '18 Uhr' (length=6)
// 'Umsetzung der Maßnahmen bis 15.11.2022?' => string 'Ja' (length=2)
// 'Unabhängige Umsetzung bereits vor Projekt?' => string 'Ja' (length=2)




//add_action('admin_head', 'test_test');

//class PE_Firmengruppe_import {
  

    function generatePet() {
    

      /* Map Rows and Loop Through Them */
      $rows   = array_map('str_getcsv', file( PE_22Uhr_Plugin_Path . 'includes/Import_sample_14.csv'));
      $header = array_shift($rows);
      $csv    = array();
      foreach($rows as $row) {
          $csv[] = array_combine($header, $row);
      }
      //var_dump($csv[0]);
      
    //print_r(get_page_by_path('g-u-t', OBJECT, 'unternehmen')->ID);
    //print_r($csv[1]["Slug"]);

    return $csv;
  }
  


  //add_action('admin_head', 'generatePet');
 //add_action('admin_head', 'insertPetPosts');

  function insertPetPosts() {

     $array = generatePet();

  foreach ($array as $single) {
      if ($single['Standorttyp Name'] == 'HAUPTHAUS'){

      $vorProjekt = $single['Unabhängige Umsetzung bereits vor Projekt?'];
      $postId = wp_insert_post(array(
          'post_type' => 'unternehmen',
          'post_title' => $single['Standortname'] .' '. $single['Werblicher Anzeige Name'],
          'post_content' => $single['Statement'] . '<h4>'. $single['Statementgeber']. '('.$single['Funktion des Statementgebers'] .'), Okt.2022</h4>', 
          'post_status' => 'publish',
          'meta_input' => array(
            'Logo Filename' => $single['Logo Filename'],
            '1-Breitengrad' => $single['Breitengrad (Komma)'],
            '2-Laengengrad' => $single['Längengrad (Komma)'],
            'Land'          => 'DE',
            'Bundesland' => $single['Bundesland'],
            'Postleitzahl' => $single['PLZ'],
            'Straße und Hausnummer' => $single['Straße'] . $single['Hausnummer'],
            'firmengruppen' => 'G.U.T.',
            'firmengruppen-hierarchie' => 1,
            'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)' =>  $vorProjekt=="ja" ? 'nein' : 'ja',
                  ),
          'tax_input' => array(
            'branche' => array(
              'Baumärkte',
              //term_exists( 'Baumärkte', 'branche'),
            ),
            'abschaltung' => $single['Um wie viel Uhr wird das Licht ausgestellt?'],
          ),
          'post_name' => $single['Slug'],
          'post_parent' => get_page_by_path('g-u-t', OBJECT, 'unternehmen')->ID
        ));

        Generate_Featured_Image( 'http://localhost:10008/wp-content/logos/'. $single['Logo Filename'], $postId  );
      }
    }


    foreach ($array as $single) {
      if($single['Standorttyp Name'] == 'ABEX'|| $single['Standorttyp Name'] == 'NIEDERLASSUNG') {
        $vorProjekt = $single['Unabhängige Umsetzung bereits vor Projekt?'];
        $postId = wp_insert_post(array(
            'post_type' => 'unternehmen',
            'post_title' => $single['Standortname'] .' '. $single['Werblicher Anzeige Name'],
            'post_content' => $single['Statement'] . '<h4>'. $single['Statementgeber']. '('.$single['Funktion des Statementgebers'] .'), Okt.2022</h4>', 
            'post_status' => 'publish',
            'meta_input' => array(
              'Logo Filename' => '--',
              '1-Breitengrad' => $single['Breitengrad (Komma)'],
              '2-Laengengrad' => $single['Längengrad (Komma)'],
              'Land'          => 'DE',
              'Bundesland' => $single['Bundesland'],
              'Postleitzahl' => $single['PLZ'],
              'Straße und Hausnummer' => $single['Straße'] . $single['Hausnummer'],
              'firmengruppen' => 'G.U.T.',
              'firmengruppen-hierarchie' => 2,
              'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)' =>  $vorProjekt=="ja" ? 'nein' : 'ja',
                    ),
            'tax_input' => array(
              'branche' => array(
                'Baumärkte',
                //term_exists( 'Baumärkte', 'branche'),
              ),
              'abschaltung' => $single['Um wie viel Uhr wird das Licht ausgestellt?'],
            ),
          'post_parent' => get_page_by_path('g-u-t/'.$single['Slug'], OBJECT, 'unternehmen')->ID
        ));
        //set_post_thumbnail( $postId, $attachment_id );    
      }
    }
  }


  function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
      $file = $upload_dir['path'] . '/' . $filename;
    else
      $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}