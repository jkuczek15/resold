import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:resold/screens/product/view.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class MapPinPill extends StatelessWidget {
  final double pinPillPosition;
  final Product selectedProduct;
  final CustomerResponse customer;
  final Position currentLocation;
  final Function dispatcher;

  MapPinPill({this.customer, this.currentLocation, this.dispatcher, this.pinPillPosition, this.selectedProduct});

  @override
  Widget build(BuildContext context) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return AnimatedPositioned(
      bottom: pinPillPosition,
      right: 0,
      left: 0,
      duration: Duration(milliseconds: 200),
      child: Align(
          alignment: Alignment.bottomCenter,
          child: InkWell(
            child: Container(
              margin: EdgeInsets.all(20),
              height: 70,
              decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.all(Radius.circular(50)),
                  boxShadow: <BoxShadow>[
                    BoxShadow(blurRadius: 20, offset: Offset.zero, color: Colors.grey.withOpacity(0.5))
                  ]),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.center,
                children: <Widget>[
                  Container(
                      width: 50,
                      height: 50,
                      margin: EdgeInsets.only(left: 10),
                      child: selectedProduct.thumbnail.isNotEmpty
                          ? ClipOval(
                              child: CachedNetworkImage(
                              imageUrl: baseProductImagePath + selectedProduct.thumbnail,
                            ))
                          : SizedBox()),
                  Expanded(
                    child: Container(
                      margin: EdgeInsets.only(left: 20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: <Widget>[
                          Text(selectedProduct.name, style: TextStyle(color: ResoldBlue)),
                          Text('${formatter.format(double.parse(selectedProduct.price).round())}',
                              style: TextStyle(fontSize: 12, color: Colors.grey)),
                        ],
                      ),
                    ),
                  )
                ],
              ),
            ),
            onTap: () {
              Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (context) => ProductPage(
                          customer: customer,
                          currentLocation: currentLocation,
                          product: selectedProduct,
                          dispatcher: dispatcher)));
            },
          )),
    );
  } // end function build
}
