<?php

/**
 * Class GeolocatorFieldTest
 * @group field
 */
class GeolocatorFieldTest extends TestCase
{
    /**
     * Constants to save some space.
     */
    const LOCATIONS = <<<TEXT
[Desc]Hey There! (mřímí) a běš nitlí? Fréř&ňoni $3500 zkedě||z tini nitrudr sepodi o báfé pěkmě?[Desc][LatLon]40.7591126,-73.979531081419[LatLon][UTM]18T:586135.37683858,4512517.6620784[UTM][Address]30 Rockefeller Plaza New York[Address][!][Desc]Lorem ipsum dolor sit amet, consectetur adipiscing elit.[Desc][LatLon]42.733398,-84.468676[LatLon][UTM]16T:707219.27661307,4734317.0614896[UTM][Address]201 Milford East Lansing[Address][!][Desc]Hey! :) [Desc][LatLon]null,null[LatLon][UTM]null:null.null[UTM][Address]15029 161st ave Grand Haven [Address]
TEXT;
    const FIELD_DEFAULT = <<<TEXT
[Desc]Default location desc[Desc][LatLon]40.449938,-86.1288379[LatLon][UTM]16T:573872.22456101,4478062.2545318[UTM][Address]1 Easy Street[Address]
TEXT;
    const FIELD_OPTIONS = <<<TEXT
[!Map!]No[!Map!][!DataView!]LatLon[!DataView!]
TEXT;

    /**
     * Test the keyword search function.
     * @group search
     */
    public function test_keywordSearch() {
        $project = self::dummyProject();
        $this->assertInstanceOf('App\Project', $project);

        $form = self::dummyForm($project->pid);
        $this->assertInstanceOf('App\Form', $form);

        $field = self::dummyField("Geolocator", $project->pid, $form->fid);
        $this->assertInstanceOf('App\Field', $field);

        $record = self::dummyRecord($project->pid, $form->fid);
        $this->assertInstanceOf('App\Record', $record);

        $field->default = self::FIELD_DEFAULT;
        $field->options = self::FIELD_OPTIONS;
        $field->save();

        $geo = new \App\GeolocatorField();
        $geo->rid = $record->rid;
        $geo->flid = $field->flid;
        $geo->locations = self::LOCATIONS;
        $geo->save();

        $args = ['hey there!'];
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertTrue($geo->keywordSearch($args, false));

        // Test search on default values (not in the location string of the actual geolocator field).
        $args = ['default location desc'];
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertTrue($geo->keywordSearch($args, false));

        $args = ['easy', 'street'];
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertTrue($geo->keywordSearch($args, false));

        $args = ['ocation']; // Partial
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertFalse($geo->keywordSearch($args, false));

        // Test values in the actual geolocator's locations.

        $args = ['&ňoni $3500 zkedě||z', 'ere'];
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertFalse($geo->keywordSearch($args, false));

        $args = ['$3500', 'Fréř&ňoni', '(mřímí)', 'zkedě||z']; // Try to break the regex.
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertTrue($geo->keywordSearch($args, false));

        $args = ['201 Milford'];
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertTrue($geo->keywordSearch($args, false));

        $args = ['15029 161st'];
        $this->assertTrue($geo->keywordSearch($args, true));
        $this->assertTrue($geo->keywordSearch($args, false));

        $args = [1234, -1, null];
        $this->assertFalse($geo->keywordSearch($args, true));
        $this->assertFalse($geo->keywordSearch($args, false));
    }

    /**
     * Test the get locations function.
     */
    public function test_getLocations() {
        $project = self::dummyProject();
        $this->assertInstanceOf('App\Project', $project);

        $form = self::dummyForm($project->pid);
        $this->assertInstanceOf('App\Form', $form);

        $field = self::dummyField("Geolocator", $project->pid, $form->fid);
        $this->assertInstanceOf('App\Field', $field);

        $record = self::dummyRecord($project->pid, $form->fid);
        $this->assertInstanceOf('App\Record', $record);

        $field->default = self::FIELD_DEFAULT;
        $field->options = self::FIELD_OPTIONS;
        $field->save();

        $geo = new \App\GeolocatorField();
        $geo->rid = $record->rid;
        $geo->flid = $field->flid;
        $geo->locations = self::LOCATIONS;
        $geo->save();

        foreach($geo->getLocations() as $location) {
            $this->assertTrue(count($location) == 4);
        }
    }
}