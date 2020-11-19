import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/widgets/loading.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/screens/account-editing/edit.dart';

class AccountPage extends StatefulWidget {
  final CustomerResponse customer;

  AccountPage(CustomerResponse customer, {Key key})
      : customer = customer,
        super(key: key);

  @override
  AccountPageState createState() => AccountPageState(customer);
}

class AccountPageState extends State<AccountPage> {
  Future<List<Product>> futureForSaleVendorProducts;
  Future<List<Product>> futureSoldVendorProducts;
  Future<Vendor> futureVendor;
  final CustomerResponse customer;
  bool displayForSale = true;
  Position currentLocation;

  AccountPageState(CustomerResponse customer) : customer = customer;

  @override
  void initState() {
    super.initState();
    futureForSaleVendorProducts = Resold.getVendorProducts(customer.vendorId, 'for-sale');
    futureSoldVendorProducts = Resold.getVendorProducts(customer.vendorId, 'sold');
    futureVendor = Resold.getVendor(customer.vendorId);
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
    return FutureBuilder<List<dynamic>>(
        future: Future.wait([futureVendor, futureForSaleVendorProducts, futureSoldVendorProducts]),
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            var vendor = snapshot.data[0];
            var forSaleProducts = snapshot.data[1];
            var soldProducts = snapshot.data[2];

            return SingleChildScrollView(
                child: Column(mainAxisAlignment: MainAxisAlignment.start, mainAxisSize: MainAxisSize.min, children: [
              Container(
                  child: Stack(children: [
                Image.asset('assets/images/login/resold-app-loginpage-background.jpg',
                    fit: BoxFit.cover, height: 602, width: 500),
                Column(children: [
                  Row(mainAxisAlignment: MainAxisAlignment.start, children: [
                    Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Container(
                          height: 115,
                          width: 115,
                          child: Padding(
                              padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                              child: CircleAvatar(
                                backgroundImage: vendor.profilePicture != 'null'
                                    ? CachedNetworkImageProvider(baseImagePath + '/' + vendor.profilePicture)
                                    : AssetImage('assets/images/avatar-placeholder.png'),
                              ))),
                      Padding(
                          padding: EdgeInsets.fromLTRB(18, 0, 0, 0),
                          child: Text(vendor.name,
                              style: new TextStyle(
                                  fontSize: 14.0,
                                  fontFamily: 'Roboto',
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white)))
                    ]),
                    Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                              child: Padding(
                                  padding: EdgeInsets.fromLTRB(20, 20, 0, 10),
                                  child: Column(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(children: [
                                          InkWell(
                                            child: Column(children: [
                                              Text(forSaleProducts.length.toString(),
                                                  style: new TextStyle(
                                                      fontSize: 24.0,
                                                      fontFamily: 'Roboto',
                                                      fontWeight: FontWeight.bold,
                                                      color: Colors.white)),
                                              Text('for sale',
                                                  style: new TextStyle(
                                                      fontSize: 14.0,
                                                      fontFamily: 'Roboto',
                                                      fontWeight: FontWeight.normal,
                                                      color: Colors.white))
                                            ]),
                                            onTap: () => {
                                              setState(() => {displayForSale = true})
                                            },
                                          ),
                                          SizedBox(width: 45),
                                          InkWell(
                                              child: Column(children: [
                                                Text(soldProducts.length.toString(),
                                                    style: new TextStyle(
                                                        fontSize: 24.0,
                                                        fontFamily: 'Roboto',
                                                        fontWeight: FontWeight.bold,
                                                        color: Colors.white)),
                                                Text('sold',
                                                    style: new TextStyle(
                                                        fontSize: 14.0,
                                                        fontFamily: 'Roboto',
                                                        fontWeight: FontWeight.normal,
                                                        color: Colors.white))
                                              ]),
                                              onTap: () => {
                                                    setState(() => {displayForSale = false})
                                                  }),
                                          SizedBox(width: 45),
                                          Column(children: [
                                            Icon(MdiIcons.tshirtCrew, color: Colors.white, size: 29.0),
                                            Text('reviews',
                                                style: new TextStyle(
                                                    fontSize: 14.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.normal,
                                                    color: Colors.white))
                                          ])
                                        ])
                                      ]))),
                          Container(
                            height: 60,
                          )
                        ])
                  ]),
                  SizedBox(height: 10),
                  ButtonTheme(
                      minWidth: 340.0,
                      height: 50.0,
                      child: RaisedButton(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadiusDirectional.circular(8)),
                        onPressed: () async {
                          // show a loading indicator
                          showDialog(
                              context: context,
                              builder: (BuildContext context) {
                                return Center(child: Loading());
                              });
                          Navigator.of(context, rootNavigator: true).pop('dialog');
                          Navigator.push(context, MaterialPageRoute(builder: (context) => EditProPage(customer)));
                        },
                        child: Text('Edit Profile',
                            style: new TextStyle(fontSize: 16.0, fontWeight: FontWeight.bold, color: Colors.white)),
                        color: Colors.black,
                        textColor: Colors.white,
                      )),
                  SizedBox(height: 10),
                  Column(
                    children: [
                      GridView.count(
                          shrinkWrap: true,
                          crossAxisCount: 2,
                          children: displayForSale
                              ? List.generate(forSaleProducts.length, (index) {
                                  var product = forSaleProducts[index];
                                  return ProductListBuilder.buildProductGridTile(
                                      context, currentLocation, product, customer, index);
                                })
                              : List.generate(soldProducts.length, (index) {
                                  var product = soldProducts[index];
                                  return ProductListBuilder.buildProductGridTile(
                                      context, currentLocation, product, customer, index);
                                })),
                    ],
                  )
                ]),
              ])),
            ]));
          } else if (snapshot.hasError) {
            return Text("${snapshot.error}");
          }
          // By default, show a loading spinner.
          return Center(child: Loading());
        });
  } // end function build
}
