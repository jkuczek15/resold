import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoder/geocoder.dart';
import 'package:iso_countries/iso_countries.dart';
import 'package:us_states/us_states.dart';

class EditAddressPage extends StatefulWidget {
  final CustomerResponse customer;

  EditAddressPage(CustomerResponse customer, {Key key})
      : customer = customer,
        super(key: key);

  @override
  EditAddressPageState createState() => EditAddressPageState(customer);
}

class EditAddressPageState extends State<EditAddressPage> {
  Position currentLocation;

  final addressLine1Controller = TextEditingController();
  final addressLine2Controller = TextEditingController();
  final cityController = TextEditingController();
  final zipController = TextEditingController();

  final updateAddressKey = GlobalKey<FormState>();
  Future<List<Address>> futureAddresses;
  CustomerResponse customer;

  Future<List<Country>> futureCountries;
  Country selectedCountry;
  List<DropdownMenuItem<String>> states = new List<DropdownMenuItem<String>>();
  String selectedState;

  EditAddressPageState(this.customer);

  @override
  void initState() {
    super.initState();
    futureCountries = IsoCountries.iso_countries;
    USStates.getNameMap().forEach((key, value) {
      states.add(DropdownMenuItem<String>(value: value, child: Text(key)));
    });
    selectedState = customer.addresses.first.region.regionCode;
    addressLine1Controller.value = TextEditingValue(
      text: customer.addresses.first.street[0],
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.firstName.length),
      ),
    );
    addressLine2Controller.value = TextEditingValue(
      text: customer.addresses.first.street.length > 1 ? customer.addresses.first.street[1] : '',
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.lastName.length),
      ),
    );
    cityController.value = TextEditingValue(
      text: customer.addresses.first.city,
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.email.length),
      ),
    );
    zipController.value = TextEditingValue(
      text: customer.addresses.first.postcode,
      selection: TextSelection.fromPosition(
        TextPosition(offset: customer.email.length),
      ),
    );
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if (this.mounted) {
        setState(() {
          currentLocation = location;
        });
      }
    });
  } // end function initState

  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, CustomerResponse>(
        converter: (state) => state.customer,
        builder: (context, dispatcher, newCustomer) {
          customer = newCustomer;
          return Scaffold(
              appBar: AppBar(
                title: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Align(
                        alignment: Alignment.centerLeft,
                        child: Text('Change Address', style: new TextStyle(color: Colors.white)))
                  ],
                ),
                iconTheme: IconThemeData(
                  color: Colors.white, // change your color here
                ),
                backgroundColor: ResoldBlue,
              ),
              body: SingleChildScrollView(
                  child: Column(
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                    Center(
                      child: Column(
                        children: [
                          Form(
                            key: updateAddressKey,
                            child: Column(children: <Widget>[
                              Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: TextFormField(
                                  controller: addressLine1Controller,
                                  decoration: InputDecoration(
                                    labelText: 'Address Line 1 *',
                                    labelStyle: TextStyle(color: ResoldBlue, fontSize: 16),
                                    enabledBorder:
                                        UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    focusedBorder:
                                        UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                  ),
                                  style: TextStyle(color: Colors.black, fontSize: 16),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter an address.';
                                    }
                                    return null;
                                  },
                                ),
                              ),
                              Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: TextFormField(
                                    controller: addressLine2Controller,
                                    decoration: InputDecoration(
                                      labelText: 'Adddress Line 2',
                                      labelStyle: TextStyle(color: ResoldBlue, fontSize: 16),
                                      enabledBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                      focusedBorder:
                                          UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                      border:
                                          UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    ),
                                    style: TextStyle(color: Colors.black, fontSize: 16)),
                              ),
                              Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: TextFormField(
                                  controller: cityController,
                                  decoration: InputDecoration(
                                    labelText: 'City *',
                                    labelStyle: TextStyle(color: ResoldBlue, fontSize: 16),
                                    enabledBorder:
                                        UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    focusedBorder:
                                        UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                  ),
                                  style: TextStyle(color: Colors.black, fontSize: 16),
                                  validator: (value) {
                                    if (value.isEmpty) {
                                      return 'Please enter a city.';
                                    }
                                    return null;
                                  },
                                ),
                              ),
                              SizedBox(height: 10),
                              Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: Container(
                                    child: DropdownButton<String>(
                                        isExpanded: true,
                                        hint: Text('Select state...', style: TextStyle(color: Colors.black)),
                                        value: selectedState,
                                        style: TextStyle(color: Colors.black, fontSize: 16),
                                        underline: Container(
                                          height: 2,
                                          color: ResoldBlue,
                                        ),
                                        onChanged: (String newValue) {
                                          setState(() {
                                            selectedState = newValue;
                                          });
                                        },
                                        items: states)),
                              ),
                              Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: TextFormField(
                                  controller: zipController,
                                  decoration: InputDecoration(
                                    labelText: 'Zip/Postal Code',
                                    labelStyle: TextStyle(color: ResoldBlue),
                                    enabledBorder:
                                        UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    focusedBorder:
                                        UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                    border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
                                  ),
                                  style: TextStyle(color: Colors.black, fontSize: 16),
                                ),
                              ),
                              SizedBox(height: 15),
                              FutureBuilder<List<Country>>(
                                future: futureCountries,
                                builder: (context, snapshot) {
                                  if (snapshot.hasData) {
                                    selectedCountry =
                                        snapshot.data.where((element) => element.countryCode == 'US').first;
                                    return Padding(
                                      padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                      child: Container(
                                          child: DropdownButton<Country>(
                                        isExpanded: true,
                                        hint: Text('Select country...', style: TextStyle(color: Colors.black)),
                                        value: selectedCountry,
                                        style: TextStyle(color: Colors.black, fontSize: 16),
                                        underline: Container(
                                          height: 2,
                                          color: ResoldBlue,
                                        ),
                                        onChanged: (Country newValue) {
                                          setState(() {
                                            selectedCountry = newValue;
                                          });
                                        },
                                        items: snapshot.data.map<DropdownMenuItem<Country>>((Country country) {
                                          return DropdownMenuItem<Country>(
                                            value: country,
                                            child: Text(country.name),
                                          );
                                        }).toList(),
                                      )),
                                    );
                                  } else {
                                    return SizedBox();
                                  }
                                },
                              ),
                              Padding(
                                padding: EdgeInsets.fromLTRB(50, 10, 50, 10),
                                child: ButtonTheme(
                                  minWidth: double.infinity,
                                  child: RaisedButton(
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                                    onPressed: () async {
                                      // TODO: change address
                                    },
                                    child: Text('Save',
                                        style: new TextStyle(
                                            fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
                                    padding: EdgeInsets.fromLTRB(50, 20, 50, 20),
                                    color: Colors.black,
                                    textColor: Colors.white,
                                  ),
                                ),
                              ),
                            ]),
                          ),
                        ],
                      ),
                    )
                  ])));
        });
  } // end function build
}
