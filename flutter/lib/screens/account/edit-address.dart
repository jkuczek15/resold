import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/state/actions/set-account-state.dart';
import 'package:resold/state/actions/set-customer.dart';
import 'package:resold/state/screens/account-state.dart';
import 'package:resold/view-models/request/magento/customer-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:geocoder/geocoder.dart';
import 'package:iso_countries/iso_countries.dart';
import 'package:resold/widgets/loading.dart';
import 'package:us_states/us_states.dart';

class EditAddressPage extends StatefulWidget {
  final CustomerResponse customer;
  final Function dispatcher;

  EditAddressPage(CustomerResponse customer, Function dispatcher, {Key key})
      : customer = customer,
        dispatcher = dispatcher,
        super(key: key);

  @override
  EditAddressPageState createState() => EditAddressPageState(customer, dispatcher);
}

class EditAddressPageState extends State<EditAddressPage> {
  final TextEditingController addressLine1Controller = TextEditingController();
  final TextEditingController addressLine2Controller = TextEditingController();
  final TextEditingController cityController = TextEditingController();
  final TextEditingController zipController = TextEditingController();
  final Function dispatcher;

  final updateAddressKey = GlobalKey<FormState>();
  Future<List<Address>> futureAddresses;
  CustomerResponse customer;

  Future<List<Country>> futureCountries;
  List<Country> countries;
  Country selectedCountry;
  List<DropdownMenuItem<String>> states = new List<DropdownMenuItem<String>>();
  String selectedState;
  bool firstBuild = true;

  EditAddressPageState(this.customer, this.dispatcher);

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
  } // end function initState

  @override
  Widget build(BuildContext context) {
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
                                border: UnderlineInputBorder(borderSide: BorderSide(color: ResoldBlue, width: 1.5)),
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
                              if (firstBuild) {
                                countries = snapshot.data;
                                selectedCountry = countries.where((country) => country.countryCode == 'US').first;
                                firstBuild = false;
                              } // end if first build
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
                                showDialog(
                                    context: context,
                                    builder: (BuildContext context) {
                                      return Center(child: Loading());
                                    });
                                customer.addresses.first.street[0] = addressLine1Controller.text;

                                if (addressLine2Controller.text.isNotEmpty) {
                                  if (customer.addresses.first.street.length == 1) {
                                    customer.addresses.first.street.add(addressLine2Controller.text);
                                  } else {
                                    customer.addresses.first.street[1] = addressLine2Controller.text;
                                  } // end if we have an address line 2
                                } // end if we have an address line 2

                                // set the customer's address
                                customer.addresses.first.city = cityController.text;
                                customer.addresses.first.countryId = selectedCountry.countryCode;
                                customer.addresses.first.region.regionCode = selectedState;
                                customer.addresses.first.region.region = USStates.getName(selectedState);

                                // try to parse the region ID
                                try {
                                  customer.addresses.first.region.regionId = int.tryParse(
                                      await Resold.getRegionId(selectedState, selectedCountry.countryCode));
                                } catch (exception) {
                                  return showDialog<void>(
                                      context: context,
                                      barrierDismissible: false,
                                      builder: (BuildContext context) {
                                        return AlertDialog(
                                            title: Text('Countries outside of the US are not supported.'),
                                            actions: <Widget>[
                                              FlatButton(
                                                  child: Text(
                                                    'Ok',
                                                    style: TextStyle(color: ResoldBlue),
                                                  ),
                                                  onPressed: () {
                                                    setState(() {
                                                      selectedCountry = countries
                                                          .where((country) => country.countryCode == 'US')
                                                          .first;
                                                    });
                                                    Navigator.of(context, rootNavigator: true).pop('dialog');
                                                    Navigator.pop(context);
                                                  })
                                            ]);
                                      });
                                } // end if we were able to parse a region id

                                // update the customer with the new address
                                await Magento.updateCustomer(
                                    customer.token,
                                    customer.id,
                                    CustomerRequest(
                                        email: customer.email,
                                        firstname: customer.firstName,
                                        lastname: customer.lastName,
                                        addresses: customer.addresses),
                                    customer.password);

                                // dispatch update customer state action
                                dispatcher(SetCustomerAction(customer));
                                dispatcher(SetAccountStateAction(await AccountState.initialState(customer)));

                                // navigate
                                Navigator.of(context, rootNavigator: true).pop('dialog');
                                Navigator.pop(context);
                              },
                              child: Text('Save',
                                  style:
                                      new TextStyle(fontSize: 20.0, fontWeight: FontWeight.bold, color: Colors.white)),
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
  } // end function build
}
