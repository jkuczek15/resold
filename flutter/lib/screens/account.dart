import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/constants/url-config.dart';
import 'package:geolocator/geolocator.dart';

class AccountPage extends StatefulWidget {
  final CustomerResponse customer;

  AccountPage(CustomerResponse customer, {Key key}) : customer = customer, super(key: key);

  @override
  AccountPageState createState() => AccountPageState(customer);
}

class AccountPageState extends State<AccountPage> {

  Future<List<Product>> futureForSaleVendorProducts;
  Future<List<Product>> futureSoldVendorProducts;
  Future<Vendor> futureVendor;
  final CustomerResponse customer;
  Position currentLocation;

  AccountPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    futureForSaleVendorProducts = Resold.getVendorProducts(customer.vendorId, 'for-sale');
    futureSoldVendorProducts = Resold.getVendorProducts(customer.vendorId, 'sold');
    futureVendor = Resold.getVendor(customer.vendorId);
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      if(this.mounted) {
        setState(() {
          currentLocation = location;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return ListView(children: [
      Container(
          color: const Color(0xff41b8ea),
          height: 205.0,
          child: FutureBuilder<Vendor>(
            future: futureVendor,
            builder: (context, snapshot) {
              if (snapshot.hasData) {
                return Column (
                  children: [
                    Row (
                    mainAxisAlignment: MainAxisAlignment.start,
                    children: [
                      Column (
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container (
                                height: 115,
                                width: 115,
                                child: Padding (
                                    padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                    child: CircleAvatar (
                                      backgroundImage: NetworkImage(baseImagePath + '/' + snapshot.data.profilePicture),
                                    )
                                )
                            ),
                            Padding(
                                padding: EdgeInsets.fromLTRB(18, 0, 0, 0),
                                child: Text(snapshot.data.name, style: new TextStyle(
                                    fontSize: 14.0,
                                    fontFamily: 'Roboto',
                                    fontWeight: FontWeight.bold,
                                    color: Colors.white
                                ))
                            )
                          ]
                      ),
                      Column (
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container (
                                child: Padding (
                                    padding: EdgeInsets.fromLTRB(20, 20, 0, 10),
                                    child: Column (
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row (
                                              children: [
                                                FutureBuilder<List<Product>> (
                                                    future: futureForSaleVendorProducts,
                                                    builder: (context, snapshot) {
                                                      if(snapshot.hasData) {
                                                        return Column (
                                                            children: [
                                                              Text(snapshot.data.length.toString(), style: new TextStyle(
                                                                  fontSize: 24.0,
                                                                  fontFamily: 'Roboto',
                                                                  fontWeight: FontWeight.bold,
                                                                  color: Colors.white
                                                              )),
                                                              Text('for sale', style: new TextStyle(
                                                                  fontSize: 14.0,
                                                                  fontFamily: 'Roboto',
                                                                  fontWeight: FontWeight.normal,
                                                                  color: Colors.white
                                                              ))
                                                            ]
                                                        );
                                                      } else {
                                                        return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                                                      }
                                                    }
                                                ),
                                                SizedBox(width: 35),
                                                FutureBuilder<List<Product>> (
                                                    future: futureSoldVendorProducts,
                                                    builder: (context, snapshot) {
                                                      if(snapshot.hasData) {
                                                        return Column (
                                                            children: [
                                                              Text(snapshot.data.length.toString(), style: new TextStyle(
                                                                  fontSize: 24.0,
                                                                  fontFamily: 'Roboto',
                                                                  fontWeight: FontWeight.bold,
                                                                  color: Colors.white
                                                              )),
                                                              Text('sold', style: new TextStyle(
                                                                  fontSize: 14.0,
                                                                  fontFamily: 'Roboto',
                                                                  fontWeight: FontWeight.normal,
                                                                  color: Colors.white
                                                              ))
                                                            ]
                                                        );
                                                      } else {
                                                        return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                                                      }
                                                    }
                                                ),
                                                SizedBox(width: 35),
                                                FutureBuilder<List<Product>> (
                                                    future: futureSoldVendorProducts,
                                                    builder: (context, snapshot) {
                                                      if(snapshot.hasData) {
                                                        return Column (
                                                            children: [
                                                              Icon(MdiIcons.tshirtCrew, color: Colors.white, size: 29.0),
                                                              Text('best seller', style: new TextStyle(
                                                                  fontSize: 14.0,
                                                                  fontFamily: 'Roboto',
                                                                  fontWeight: FontWeight.normal,
                                                                  color: Colors.white
                                                              ))
                                                            ]
                                                        );
                                                      } else {
                                                        return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                                                      }
                                                    }
                                                ),
                                              ]
                                          )
                                        ]
                                    )
                                )
                            ),
                            Container (
                              height: 60,
                              child: Padding (
                                padding: EdgeInsets.fromLTRB(20, 20, 0, 10),
                                child: Column (
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                  Text(snapshot.data.about, style: new TextStyle(
                                      fontSize: 14.0,
                                      fontFamily: 'Roboto',
                                      fontWeight: FontWeight.normal,
                                      color: Colors.white
                                  ))
                                ]
                              )
                            )
                          )
                        ]
                      )
                  ]
              ),
              SizedBox(height: 10),
              ButtonTheme (
                    minWidth: 340.0,
                    height: 50.0,
                    child: RaisedButton(
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadiusDirectional.circular(8)
                      ),
                      onPressed: () async {
                        // show a loading indicator
                        showDialog(
                            context: context,
                            builder: (BuildContext context) {
                              return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                            }
                        );
                        Navigator.of(context, rootNavigator: true).pop('dialog');
                      },
                      child: Text('Edit Profile',
                          style: new TextStyle(
                              fontSize: 16.0,
                              fontWeight: FontWeight.bold,
                              color: Colors.white
                          )
                      ),
                      color: Colors.black,
                      textColor: Colors.white,
                    )
                    ),
                  ]
                );
              } else if (snapshot.hasError) {
                return Text("${snapshot.error}");
              }
              // By default, show a loading spinner.
              return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
            },
          )
      ),
      DefaultTabController(
            length: 2,
            initialIndex: 0,
            child: Column(
              children: [
                TabBar(
                  indicatorColor: const Color(0xff41b8ea),
                tabs: [
                  Tab(icon: Icon(MdiIcons.signRealEstate, semanticLabel: 'For Sale'), text: 'For Sale'),
                  Tab(icon: Icon(MdiIcons.clipboardText, semanticLabel: 'Sold'), text: 'Sold')
                ],
              ),
              Container (
                height: 300.0,
                child: TabBarView(
                children: [
                  FutureBuilder<List<Product>>(
                    future: futureForSaleVendorProducts,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        return GridView.count(
                            crossAxisCount: 2,
                            childAspectRatio: 1.6,
                            children: List.generate(snapshot.data.length, (index) {
                              var product = snapshot.data[index];
                              return ProductListBuilder.buildProductGridTile(context, currentLocation, product, index);
                            })
                        );
                      } else if (snapshot.hasError) {
                        return Text("${snapshot.error}");
                      }
                      // By default, show a loading spinner.
                      return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                    },
                  ),
                  FutureBuilder<List<Product>>(
                    future: futureSoldVendorProducts,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        return GridView.count(
                          crossAxisCount: 2,
                          childAspectRatio: 3/2,
                          children: List.generate(snapshot.data.length, (index) {
                            var product = snapshot.data[index];
                            return ProductListBuilder.buildProductGridTile(context, currentLocation, product, index);
                          }),
                        );
                      } else if (snapshot.hasError) {
                        return Text("${snapshot.error}");
                      }
                      // By default, show a loading spinner.
                      return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                    },
                  )
                ],
                )
              )
            ],
        ))
    ]);
  }
}
