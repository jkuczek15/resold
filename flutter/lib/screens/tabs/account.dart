import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:resold/state/actions/set-account-state.dart';
import 'package:resold/state/screens/account-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/screens/account/edit.dart';
import 'package:resold/widgets/grid/product-grid.dart';

class AccountPage extends StatelessWidget {
  final CustomerResponse customer;
  final Vendor vendor;
  final Position currentLocation;
  final List<Product> forSaleProducts;
  final List<Product> soldProducts;
  final bool displayForSale;
  final Function dispatcher;

  AccountPage(
      {this.customer,
      this.vendor,
      this.currentLocation,
      this.forSaleProducts,
      this.soldProducts,
      this.displayForSale,
      this.dispatcher});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
          child: Column(
              mainAxisAlignment: MainAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
            Container(
                child: Stack(children: [
              Image.asset(
                  'assets/images/login/resold-app-loginpage-background.jpg',
                  fit: BoxFit.fill),
              Column(children: [
                Row(mainAxisAlignment: MainAxisAlignment.start, children: [
                  Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                            height: 115,
                            width: 115,
                            child: Padding(
                                padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                child: CircleAvatar(
                                  backgroundImage: vendor.profilePicture !=
                                          'null'
                                      ? CachedNetworkImageProvider(
                                          vendor.getImagePath())
                                      : AssetImage(
                                          'assets/images/avatar-placeholder.png'),
                                ))),
                        Padding(
                            padding: EdgeInsets.fromLTRB(18, 0, 0, 0),
                            child: Text(customer.fullName,
                                style: new TextStyle(
                                    fontSize: 14.0,
                                    fontFamily: 'Raleway',
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
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      SizedBox(height: 10),
                                      Row(children: [
                                        SizedBox(width: 10),
                                        InkWell(
                                          child: Column(children: [
                                            Text(
                                                forSaleProducts.length
                                                    .toString(),
                                                style: new TextStyle(
                                                    fontSize: 32.0,
                                                    fontFamily: 'Raleway',
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.white)),
                                            Text('for sale',
                                                style: new TextStyle(
                                                    fontSize: 20.0,
                                                    fontFamily: 'Raleway',
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.white))
                                          ]),
                                          onTap: () => dispatcher(
                                              SetAccountStateAction(
                                                  AccountState(
                                                      displayForSale: true,
                                                      vendor: vendor,
                                                      forSaleProducts:
                                                          forSaleProducts,
                                                      soldProducts:
                                                          soldProducts))),
                                        ),
                                        SizedBox(width: 60),
                                        InkWell(
                                          child: Column(children: [
                                            Text(soldProducts.length.toString(),
                                                style: new TextStyle(
                                                    fontSize: 32.0,
                                                    fontFamily: 'Raleway',
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.white)),
                                            Text('sold',
                                                style: new TextStyle(
                                                    fontSize: 20.0,
                                                    fontFamily: 'Raleway',
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.white))
                                          ]),
                                          onTap: () => dispatcher(
                                            SetAccountStateAction(AccountState(
                                                displayForSale: false,
                                                vendor: vendor,
                                                forSaleProducts:
                                                    forSaleProducts,
                                                soldProducts: soldProducts)),
                                          ),
                                        )
                                      ])
                                    ]))),
                        Container(
                          height: 30,
                        )
                      ])
                ]),
                SizedBox(height: 10),
                ButtonTheme(
                    minWidth: 340.0,
                    height: 50.0,
                    child: RaisedButton(
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadiusDirectional.circular(8)),
                      onPressed: () async {
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (context) => EditProfilePage(customer,
                                    vendor, currentLocation, dispatcher)));
                      },
                      child: Text('Edit Profile',
                          style: new TextStyle(
                              fontSize: 16.0,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                              fontFamily: 'Raleway')),
                      color: Colors.black,
                      textColor: Colors.white,
                    )),
                SizedBox(height: 10),
                Container(
                    child: Column(
                  children: [
                    displayForSale && forSaleProducts.length == 0
                        ? Center(
                            child: Padding(
                                padding: EdgeInsets.fromLTRB(0, 120, 0, 0),
                                child: Text(
                                    'You haven\'t listed any items for sale.',
                                    style: new TextStyle(
                                        fontSize: 16.0,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.white,
                                        fontFamily: 'Raleway'))))
                        : !displayForSale && soldProducts.length == 0
                            ? Center(
                                child: Padding(
                                    padding: EdgeInsets.fromLTRB(0, 120, 0, 0),
                                    child: Text('You haven\'t sold any items.',
                                        style: new TextStyle(
                                            fontSize: 16.0,
                                            fontWeight: FontWeight.bold,
                                            color: Colors.white,
                                            fontFamily: 'Raleway'))))
                            : ProductGrid(
                                customer: customer,
                                currentLocation: currentLocation,
                                products: displayForSale
                                    ? forSaleProducts
                                    : soldProducts,
                                dispatcher: dispatcher)
                  ],
                ))
              ]),
            ])),
          ])),
    );
  } // end function build
}
