<?php
header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>";
?>
<wsdl:definitions xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="http://ws.wsiotramite.segdi.gob.pe/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:ns1="http://schemas.xmlsoap.org/soap/http" name="Tramite" targetNamespace="http://ws.wsiotramite.segdi.gob.pe/">
  <wsdl:types>
    <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://ws.wsiotramite.segdi.gob.pe/" elementFormDefault="unqualified" targetNamespace="http://ws.wsiotramite.segdi.gob.pe/" version="1.0">

      <xs:element name="cargoResponse" type="tns:cargoResponse" />

      <xs:element name="cargoResponseResponse" type="tns:cargoResponseResponse" />

      <xs:element name="consultarTramiteResponse" type="tns:consultarTramiteResponse" />

      <xs:element name="consultarTramiteResponseResponse" type="tns:consultarTramiteResponseResponse" />

      <xs:element name="recepcionarTramiteResponse" type="tns:recepcionarTramiteResponse" />

      <xs:element name="recepcionarTramiteResponseResponse" type="tns:recepcionarTramiteResponseResponse" />

      <xs:complexType name="cargoResponse">
        <xs:sequence>
          <xs:element name="request" type="tns:CargoTramite" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="CargoTramite">
        <xs:sequence>
          <xs:element minOccurs="0" name="vcuo" type="xs:string" />
          <xs:element minOccurs="0" name="vcuoref" type="xs:string" />
          <xs:element minOccurs="0" name="vnumregstd" type="xs:string" />
          <xs:element minOccurs="0" name="vanioregstd" type="xs:string" />
          <xs:element minOccurs="0" name="dfecregstd" type="xs:dateTime" />
          <xs:element minOccurs="0" name="vuniorgstd" type="xs:string" />
          <xs:element minOccurs="0" name="vusuregstd" type="xs:string" />
          <xs:element minOccurs="0" name="bcarstd" type="xs:base64Binary" />
          <xs:element minOccurs="0" name="vobs" type="xs:string" />
          <xs:element minOccurs="0" name="cflgest" type="xs:string" />
          <xs:element minOccurs="0" name="vdesanxstdrec" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="cargoResponseResponse">
        <xs:sequence>
          <xs:element minOccurs="0" name="return" type="tns:RespuestaCargoTramite" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="RespuestaCargoTramite">
        <xs:sequence>
          <xs:element minOccurs="0" name="vcodres" type="xs:string" />
          <xs:element minOccurs="0" name="vdesres" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="consultarTramiteResponse">
        <xs:sequence>
          <xs:element minOccurs="0" name="request" type="tns:ConsultaTramite" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="ConsultaTramite">
        <xs:sequence>
          <xs:element minOccurs="0" name="vrucentrem" type="xs:string" />
          <xs:element minOccurs="0" name="vrucentrec" type="xs:string" />
          <xs:element minOccurs="0" name="vcuo" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="consultarTramiteResponseResponse">
        <xs:sequence>
          <xs:element minOccurs="0" name="return" type="tns:RespuestaConsultaTramite" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="RespuestaConsultaTramite">
        <xs:sequence>
          <xs:element minOccurs="0" name="vcodres" type="xs:string" />
          <xs:element minOccurs="0" name="vdesres" type="xs:string" />
          <xs:element minOccurs="0" name="vcuo" type="xs:string" />
          <xs:element minOccurs="0" name="vcuoref" type="xs:string" />
          <xs:element minOccurs="0" name="vnumregstd" type="xs:string" />
          <xs:element minOccurs="0" name="vanioregstd" type="xs:string" />
          <xs:element minOccurs="0" name="vuniorgstd" type="xs:string" />
          <xs:element minOccurs="0" name="dfecregstd" type="xs:dateTime" />
          <xs:element minOccurs="0" name="vusuregstd" type="xs:string" />
          <xs:element minOccurs="0" name="bcarstd" type="xs:base64Binary" />
          <xs:element minOccurs="0" name="vobs" type="xs:string" />
          <xs:element minOccurs="0" name="cflgest" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="recepcionarTramiteResponse">
        <xs:sequence>
          <xs:element minOccurs="0" name="request" type="tns:RecepcionTramite" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="RecepcionTramite">
        <xs:sequence>
          <xs:element minOccurs="0" name="vrucentrem" type="xs:string" />
          <xs:element minOccurs="0" name="vrucentrec" type="xs:string" />
          <xs:element minOccurs="0" name="vnomentemi" type="xs:string" />
          <xs:element minOccurs="0" name="vuniorgrem" type="xs:string" />
          <xs:element minOccurs="0" name="vcuo" type="xs:string" />
          <xs:element minOccurs="0" name="vcuoref" type="xs:string" />
          <xs:element minOccurs="0" name="ccodtipdoc" type="xs:string" />
          <xs:element minOccurs="0" name="vnumdoc" type="xs:string" />
          <xs:element minOccurs="0" name="dfecdoc" type="xs:dateTime" />
          <xs:element minOccurs="0" name="vuniorgdst" type="xs:string" />
          <xs:element minOccurs="0" name="vnomdst" type="xs:string" />
          <xs:element minOccurs="0" name="vnomcardst" type="xs:string" />
          <xs:element minOccurs="0" name="vasu" type="xs:string" />
          <xs:element name="snumanx" type="xs:int" />
          <xs:element name="snumfol" type="xs:int" />
          <xs:element minOccurs="0" name="vurldocanx" type="xs:string" />
          <xs:element minOccurs="0" name="bpdfdoc" type="xs:base64Binary" />
          <xs:element minOccurs="0" name="vnomdoc" type="xs:string" />
          <xs:element maxOccurs="unbounded" minOccurs="0" name="lstanexos" nillable="true" type="tns:documentoAnexo" />
          <xs:element minOccurs="0" name="ctipdociderem" type="xs:string" />
          <xs:element minOccurs="0" name="vnumdociderem" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="documentoAnexo">
        <xs:sequence>
          <xs:element minOccurs="0" name="vnomdoc" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="recepcionarTramiteResponseResponse">
        <xs:sequence>
          <xs:element minOccurs="0" name="return" type="tns:RespuestaTramite" />
        </xs:sequence>
      </xs:complexType>

      <xs:complexType name="RespuestaTramite">
        <xs:sequence>
          <xs:element minOccurs="0" name="vcodres" type="xs:string" />
          <xs:element minOccurs="0" name="vdesres" type="xs:string" />
        </xs:sequence>
      </xs:complexType>

    </xs:schema>
  </wsdl:types>
  <wsdl:message name="consultarTramiteResponse">
    <wsdl:part element="tns:consultarTramiteResponse" name="parameters">
    </wsdl:part>
  </wsdl:message>
  <wsdl:message name="recepcionarTramiteResponse">
    <wsdl:part element="tns:recepcionarTramiteResponse" name="parameters">
    </wsdl:part>
  </wsdl:message>
  <wsdl:message name="consultarTramiteResponseResponse">
    <wsdl:part element="tns:consultarTramiteResponseResponse" name="parameters">
    </wsdl:part>
  </wsdl:message>
  <wsdl:message name="cargoResponse">
    <wsdl:part element="tns:cargoResponse" name="parameters">
    </wsdl:part>
  </wsdl:message>
  <wsdl:message name="recepcionarTramiteResponseResponse">
    <wsdl:part element="tns:recepcionarTramiteResponseResponse" name="parameters">
    </wsdl:part>
  </wsdl:message>
  <wsdl:message name="cargoResponseResponse">
    <wsdl:part element="tns:cargoResponseResponse" name="parameters">
    </wsdl:part>
  </wsdl:message>
  <wsdl:portType name="Tramite">
    <wsdl:operation name="cargoResponse">
      <wsdl:input message="tns:cargoResponse" name="cargoResponse">
      </wsdl:input>
      <wsdl:output message="tns:cargoResponseResponse" name="cargoResponseResponse">
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="consultarTramiteResponse">
      <wsdl:input message="tns:consultarTramiteResponse" name="consultarTramiteResponse">
      </wsdl:input>
      <wsdl:output message="tns:consultarTramiteResponseResponse" name="consultarTramiteResponseResponse">
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="recepcionarTramiteResponse">
      <wsdl:input message="tns:recepcionarTramiteResponse" name="recepcionarTramiteResponse">
      </wsdl:input>
      <wsdl:output message="tns:recepcionarTramiteResponseResponse" name="recepcionarTramiteResponseResponse">
      </wsdl:output>
    </wsdl:operation>
  </wsdl:portType>
  <wsdl:binding name="TramiteSoapBinding" type="tns:Tramite">
    <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http" />
    <wsdl:operation name="cargoResponse">
      <soap:operation soapAction="" style="document" />
      <wsdl:input name="cargoResponse">
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output name="cargoResponseResponse">
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="consultarTramiteResponse">
      <soap:operation soapAction="" style="document" />
      <wsdl:input name="consultarTramiteResponse">
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output name="consultarTramiteResponseResponse">
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="recepcionarTramiteResponse">
      <soap:operation soapAction="" style="document" />
      <wsdl:input name="recepcionarTramiteResponse">
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output name="recepcionarTramiteResponseResponse">
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
  </wsdl:binding>
  <wsdl:service name="Tramite">
    <wsdl:port binding="tns:TramiteSoapBinding" name="TramitePort">
      <soap:address location="http://<?php echo $host; ?>/wsiotramite/Tramite" />
    </wsdl:port>
  </wsdl:service>
</wsdl:definitions>