import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/models/product.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:resold/screens/product/view.dart';

class MapPinPill extends StatefulWidget {
  final double pinPillPosition;
  final Product selectedProduct;
  final String customerToken;

  MapPinPill({this.pinPillPosition, this.selectedProduct, this.customerToken});

  @override
  State<StatefulWidget> createState() => MapPinPillState();
}

class MapPinPillState extends State<MapPinPill> {
  @override
  Widget build(BuildContext context) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return AnimatedPositioned(
      bottom: widget.pinPillPosition,
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
                      child: widget.selectedProduct.thumbnail.isNotEmpty
                          ? ClipOval(
                              child: CachedNetworkImage(
                              imageUrl: baseProductImagePath + widget.selectedProduct.thumbnail,
                            ))
                          : SizedBox()),
                  Expanded(
                    child: Container(
                      margin: EdgeInsets.only(left: 20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: <Widget>[
                          Text(widget.selectedProduct.name, style: TextStyle(color: ResoldBlue)),
                          Text('${formatter.format(double.parse(widget.selectedProduct.price).round())}',
                              style: TextStyle(fontSize: 12, color: Colors.grey)),
                        ],
                      ),
                    ),
                  )
                ],
              ),
            ),
            onTap: () {
              Navigator.push(context,
                  MaterialPageRoute(builder: (context) => ProductPage(widget.selectedProduct, widget.customerToken)));
            },
          )),
    );
  } // end function build
}
