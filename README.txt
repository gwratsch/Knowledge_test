Knowledge Test module
Drupal 7 module.

Idee:
    Using documents to create automaticly default templates for inserting information.

Options:
    - knowledge test
    - creating standard reports for collecting business information.
    - etc.


Create documents:
    node type : article
    fieldname: hoofdgroep   (noderelation. To insert the structure between documents. So this document is part of ... document )
        create the field in the node type 'article' with the next settings:
            label : a usefull title
            machine Name: field_hoofdgroep (it is inportant to use this name for selecting data from the database.)
                         If you wish to use an other field name then 
                            change in the knowledge_test.module file.
                                the word 'field_hoofdgroep' in the field machine name you chose.
            Field type: entity reference
            widget : autocomplete

            In the next settingspage:
             ...field settings:
                Number of values    :unlimited
                target type         : node
                Entity selection
                    Mode            : Simple
                    Target bundles  : Article

    Body : add content and use H1-H6 HTML tag to display the structure.


Create Input documents:
    Create the node type 'organisation'.
        name        : organisation
        If you want to use an other name for the node type then you have to change:
            In knowledge_test.js
                change "node-type-organisation" in the type machine name you chose.
            In knowledge_test.module
                change 'type == "organisation"' in the type machine name you chose.

    node type : organisation
    Title : Give a uniek title so you know what info is to be collected.
    Body : insert the target for this document.
    example: 
        - what changes have to be made to increase the organisation profit
        - repeat a dificult part of your study
        - default questions to ask new customers
        - collecting alle wishes from the customer for building a website
    Database: when de document is createdin the database a table is created and gets the document id as name.

How it works:
    When opening the organisation document you can select one of the templates you have created.
    Javascript (JS) will request the server to collect all input fields of the requested template.
    JS wil display the input fields in the body.

    The next actions will trigger JS toe send the input field to the server to save the content in the database:
        - when opening an other webpage
        - when requesting an other template
        - by using the 'opslaan' (save) button to send the content to the server without leaving the page.

    When deleting the organisation document, the table will also be deleted. 


    PS: Remember the title, in de article node, is used to search in the table if there is any comment. If you change the title
        the inserted command will not be found. Change it back and you get the inserted command.

Create a block to execute the next php function:
    <?php
       echo get_hoofdgroep_data('knowledgemenu');
    ?>
    This wil display a menu structure of het inserted articles. With de noderelation and the h1-h6 it will
    build a menu structure. These words will get a JS strigger so JS can send a request to the server for info.

Create a block to be displayed in the body of type organisation node:
    <!--?php<div id="organisationInfo"-->
    <p>Select subject to receive input fields.</p> 
    This code is needed to JS nows where to put the reseaved info.

Wish list:
- Let JS add the H1-H6 structure to the input fields to improve the structure display.?
- When using 1 field in serveral documents. How can we update 1 field and display it in all other documents?
- How can we improve the security structure for inserting and saving information.?
- Add the ckeditor options for the input fields.?
- Is it usefull to add the posibilty to select / deselect input fields.
- export and import from and to WORD to be used as template.?
- export and import of the inserted content to and from WORD.?

- Is it usefull to inform other users then the template has been changed?

- what sort of backup options do we need when inserting information and the connection is gone or
  we have to login again.? for example: saving alle information localy and whait until the connection
  is restored.
- How do we create a restore posibility and how many versions do we have to go back?
- add create block function to automaticly add the function get_hoofdgroep_data('knowledgemenu') for displaying
  the 'hoofdgroep' noderelation en h1-h6 field structure.
