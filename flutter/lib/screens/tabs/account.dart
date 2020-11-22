import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/constants/url-config.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/screens/account/edit.dart';

class AccountPage extends StatefulWidget {
  AccountPage({Key key}) : super(key: key);

  @override
  AccountPageState createState() => AccountPageState();
}

class AccountPageState extends State<AccountPage> {
  bool displayForSale = true;
  Position currentLocation;
  String imagePath;

  @override
  void initState() {
    super.initState();
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
        builder: (context, dispatcher, customer) {
          return ViewModelSubscriber<AppState, List<Product>>(
              converter: (state) => state.forSaleProducts,
              builder: (context, dispatcher, forSaleProducts) {
                return ViewModelSubscriber<AppState, List<Product>>(
                    converter: (state) => state.soldProducts,
                    builder: (context, dispatcher, soldProducts) {
                      return ViewModelSubscriber<AppState, Vendor>(
                          converter: (state) => state.vendor,
                          builder: (context, dispatcher, vendor) {
                            imagePath = baseImagePath +
                                '/' +
                                vendor.profilePicture +
                                '?d=' +
                                DateTime.now().millisecond.toString();

                            return Scaffold(
                              backgroundColor: Colors.white,
                              body: SingleChildScrollView(
                                  child: Column(
                                      mainAxisAlignment: MainAxisAlignment.start,
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                    Container(
                                        child: Stack(children: [
                                      Image.asset('assets/images/login/resold-app-loginpage-background.jpg',
                                          fit: BoxFit.cover, height: 600, width: 500),
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
                                                          ? CachedNetworkImageProvider(imagePath)
                                                          : AssetImage('assets/images/avatar-placeholder.png'),
                                                    ))),
                                            Padding(
                                                padding: EdgeInsets.fromLTRB(18, 0, 0, 0),
                                                child: Text(customer.fullName,
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
                                                              SizedBox(height: 10),
                                                              Row(children: [
                                                                SizedBox(width: 30),
                                                                InkWell(
                                                                  child: Column(children: [
                                                                    Text(forSaleProducts.length.toString(),
                                                                        style: new TextStyle(
                                                                            fontSize: 32.0,
                                                                            fontFamily: 'Roboto',
                                                                            fontWeight: FontWeight.bold,
                                                                            color: Colors.white)),
                                                                    Text('for sale',
                                                                        style: new TextStyle(
                                                                            fontSize: 20.0,
                                                                            fontFamily: 'Roboto',
                                                                            fontWeight: FontWeight.bold,
                                                                            color: Colors.white))
                                                                  ]),
                                                                  onTap: () => {
                                                                    setState(() => {displayForSale = true})
                                                                  },
                                                                ),
                                                                SizedBox(width: 60),
                                                                InkWell(
                                                                    child: Column(children: [
                                                                      Text(soldProducts.length.toString(),
                                                                          style: new TextStyle(
                                                                              fontSize: 32.0,
                                                                              fontFamily: 'Roboto',
                                                                              fontWeight: FontWeight.bold,
                                                                              color: Colors.white)),
                                                                      Text('sold',
                                                                          style: new TextStyle(
                                                                              fontSize: 20.0,
                                                                              fontFamily: 'Roboto',
                                                                              fontWeight: FontWeight.bold,
                                                                              color: Colors.white))
                                                                    ]),
                                                                    onTap: () => {
                                                                          setState(() => {displayForSale = false})
                                                                        }),
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
                                                        builder: (context) =>
                                                            EditProfilePage(customer))).then((value) => {
                                                      setState(() {
                                                        imageCache.clear();
                                                        imagePath =
                                                            imagePath + '?d=' + DateTime.now().millisecond.toString();
                                                      })
                                                    });
                                              },
                                              child: Text('Edit Profile',
                                                  style: new TextStyle(
                                                      fontSize: 16.0,
                                                      fontWeight: FontWeight.bold,
                                                      color: Colors.white)),
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
                                                        child: Text('You haven\'t listed any items for sale.',
                                                            style: new TextStyle(
                                                                fontSize: 16.0,
                                                                fontWeight: FontWeight.bold,
                                                                color: Colors.white))))
                                                : !displayForSale && soldProducts.length == 0
                                                    ? Center(
                                                        child: Padding(
                                                            padding: EdgeInsets.fromLTRB(0, 120, 0, 0),
                                                            child: Text('You haven\'t sold any items.',
                                                                style: new TextStyle(
                                                                    fontSize: 16.0,
                                                                    fontWeight: FontWeight.bold,
                                                                    color: Colors.white))))
                                                    : GridView.count(
                                                        physics: ScrollPhysics(),
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
                                        ))
                                      ]),
                                    ])),
                                  ])),
                            );
                          } // end vendor builder
                          );
                    } // end soldProducts builder
                    );
              } // end forSaleProducts builder
              );
        });
  } // end function build
}
