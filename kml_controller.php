<?php
class KmlController extends ApplicationController {
	public function indexAction(){
		$this->setParamToView("hola", $this->_cambiarPosBD());
		
		
		
	}
	
	private function _cambiarPosBD(){
		/*$arre = array("AMKL2","AMKL1","AMAL1","VMUJ1","CMGJ2","AMAG3","AMAK0","AMAG2",
			"AMXPG3","AMAF7","AMAF5","VMUF4","AMAF3","AMLF1","AMKE1",
			"AMLE3","EMOE0","AMLE2","AMLE1","VMKD1","VMKD2","VMKD3",
			"AMAC6","VMDB7","AMAB6","CMGB5","AMLB4","EMOB3","AMDB2",
			"AMKA1","AMKA2","VMUA10","VMDA9","VMUA8","VMDA7","VMUA2",
			"VMUA1","VMP1","CMG1");*/
		$arre2 = array("AMAD2","AMAD4");		
		foreach($arre2 as $filt){
			$areaq = $this->Area->findFirst("id like '$filt'");
			$lat1 = $areaq->getLattres();
			$lon1 = $areaq->getLontres();
			$lat2 = $areaq->getLatcuatro();
			$lon2 = $areaq->getLoncuatro();
			$areaq->setLontres($lon2);
			$areaq->setLoncuatro($lon1);
			$areaq->setLattres($lat2);
			$areaq->setLatcuatro($lat1);
			if($areaq->save()==false){
				$mesg = "fakka";
			}else $mesg = "Al pelo";
			
		}
		return $mesg;
		
		
	}
	
	private function _createKml() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		// Creates the root KML element and appends it to the root document.
		$node = $dom->createElementNS('http://earth.google.com/kml/2.2', 'kml');
		//Agregamos parnode a node
		$parNode = $dom->appendChild($node);
		// Creates a KML Document element and append it to the KML element.
		$dnode = $dom->createElement('Document');
		$docNode = $parNode->appendChild($dnode);
		//Se traen todos los sitios de interes de la tabla
		foreach ($this->SitioInteres->find() as $row){
			  $node = $dom->createElement('Placemark');
			  $placeNode = $docNode->appendChild($node);
			  //Añadimos nombres y descripcion al punto
			  $nameNode = $dom->createElement('name',$row->getId());
			  $placeNode->appendChild($nameNode); 
			  $descNode = $dom->createElement('description', utf8_encode($row->getDescripcion()));
			  $placeNode->appendChild($descNode);
			  // Creates a Point element.
			  $pointNode = $dom->createElement('Point');
			  $placeNode->appendChild($pointNode);
			  // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
			  $coorStr = $row->getLongitud() . ','  . $row->getLatitud();
			  $coorNode = $dom->createElement('coordinates', $coorStr);
			  $pointNode->appendChild($coorNode);			  	
		}
		//Se traen todas las areas de la base de datos
		foreach ($this->Area->find() as $row2){
			$node = $dom->createElement('Placemark');
			$placeNode = $docNode->appendChild($node);
			//Añadimos nombre y descripcion al poligono
			$nameNode = $dom->createElement('name',$row2->getId());
			$placeNode->appendChild($nameNode);
			$descNode = $dom->createElement('description', utf8_encode($row2->getDescripcion()));
			$placeNode->appendChild($descNode);
			//Creamos un objeto poligono y les asignamos los puntos
			$polygonNode = $dom->createElement('Polygon');
			$placeNode->appendChild($polygonNode);
			$extrudeNode = $dom->createElement('extrude','1');
			$polygonNode->appendChild($extrudeNode);
			$outboundNode = $dom->createElement('outerBoundaryIs');
			$polygonNode->appendChild($outboundNode);
			$linearNode = $dom->createElement('LinearRing');
			$outboundNode->appendChild($linearNode);
			//Creamos el arreglo de coordenadas en formato lng,lat
			$coorArray = $row2->getLonuno() . ','  . $row2->getLatuno().' '.$row2->getLondos() . ','  . $row2->getLatdos().' '.$row2->getLontres() . ','  . $row2->getLattres().' '.$row2->getLoncuatro() . ','  . $row2->getLatcuatro();
			$coordNode = $dom->createElement('coordinates',$coorArray);
			$linearNode->appendChild($coordNode);			
			
		}
		header('Content-type: application/vnd.google-earth.kml+xml');
		return $kmlOutput = $dom->saveXML();
	}
	//Controlador en la que se obtienen todos los marcadores
	public function generateKmlAction(){	
		$this->setParamToView("kmlfile", $this->_createKml());
				
	}
	
	
}
?>