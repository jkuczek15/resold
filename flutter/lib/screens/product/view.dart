import 'package:flutter/material.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:resold/constants/url-config.dart';
import 'package:intl/intl.dart';
import 'package:resold/widgets/read-more-text.dart';

class ProductPage extends StatefulWidget {
  final Product product;

  ProductPage(Product product, {Key key}) : product = product, super(key: key);

  @override
  ProductPageState createState() => ProductPageState(this.product);
}

class ProductPageState extends State<ProductPage> {

  Product product;
  Future<List<String>> futureImages;

  ProductPageState(Product product) {
    this.product = product;
  }

  @override
  void initState() {
    super.initState();
    setState(() {
      if(this.mounted) {
        futureImages = Resold.getProductImages(product.id);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    var formatter = new NumberFormat("\$###,###", "en_US");
    return WillPopScope(
      child: Scaffold(
        appBar: AppBar (
          title: Text(product.name, style: new TextStyle(color: Colors.white)),
          backgroundColor: const Color(0xff41b8ea),
          iconTheme: IconThemeData(
            color: Colors.white, //change your color here
          ),
        ),
        body: Stack (
          children: [
            FutureBuilder<List<String>>(
                future: futureImages,
                builder: (context, snapshot) {
                  if (snapshot.hasData) {
                    Widget imageElement;
                    if(snapshot.data.length == 1) {
                      imageElement = FadeInImage(
                        image: NetworkImage(baseImagePath + snapshot.data[0]),
                        placeholder: AssetImage('assets/images/placeholder-image.png'),
                        fit: BoxFit.cover
                      );
                    } else {
                        imageElement = CarouselSlider(
                          options: CarouselOptions(height: 400.0),
                          items: snapshot.data.map((image) {
                              return Builder(
                                builder: (BuildContext context) {
                                  return Container(
                                    width: MediaQuery.of(context).size.width,
                                    margin: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                                    decoration: BoxDecoration(color: const Color(0xff41b8ea)
                                  ),
                                  child: FadeInImage(image: NetworkImage(baseImagePath + image), placeholder: AssetImage('assets/images/placeholder-image.png'), fit: BoxFit.cover)
                                );
                              },
                            );
                          }).toList()
                        );
                      }

                      return SingleChildScrollView (
                        child: Column (
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            imageElement,
                            Padding(
                              padding: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                              child: Column (
                                children: [
                                  Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Column (
                                            mainAxisAlignment: MainAxisAlignment.start,
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Container(
                                                padding: new EdgeInsets.only(right: 13.0),
                                                width: 300,
                                                child: new Text(
                                                  product.name,
                                                  overflow: TextOverflow.fade,
                                                  style: new TextStyle(
                                                    fontSize: 14.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.normal,
                                                  ),
                                                ),
                                              ),
                                              Container(
                                                padding: new EdgeInsets.only(right: 13.0),
                                                width: 300,
                                                child: new Text(
                                                  product.titleDescription ?? "",
                                                  overflow: TextOverflow.fade,
                                                  style: new TextStyle(
                                                    fontSize: 14.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.normal,
                                                  ),
                                                ),
                                              ),
                                            ]
                                        ),
                                        Column (
                                            mainAxisAlignment: MainAxisAlignment.end,
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(formatter.format(double.parse(product.price).round()),
                                                  style: new TextStyle(
                                                    fontSize: 12.0,
                                                    fontFamily: 'Roboto',
                                                    fontWeight: FontWeight.bold,
                                                  )
                                              )
                                            ]
                                        )
                                      ]
                                  ),
                                  SizedBox(height: 10),
                                  Container (
                                    width: 500,
                                    child:
                                    ReadMoreText (
                                      cleanDescription(product.description),
                                      trimLength: 100,
                                      colorClickableText: const Color(0xff41b8ea),
                                      textAlign: TextAlign.left,
                                    ),
                                  ),
                                  SizedBox(height: 10),
                                  RaisedButton(
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
                                    child: Text('Buy',
                                      style: new TextStyle(
                                          fontSize: 20.0,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.white
                                      )
                                    ),
                                    padding: EdgeInsets.fromLTRB(150, 30, 150, 30),
                                    color: Colors.black,
                                    textColor: Colors.white,
                                  )
                                ]
                              )
                            )
                          ],
                        )
                    );
                  } else {
                    return Column (
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        Center(
                          child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea))
                        )
                      ]
                    );
                  }
                }
            )
          ],
        )
      ),
      onWillPop: () async {
        Navigator.pop(context);
        return false;
      }
    );
  }

  String cleanDescription (String description) {
    return description.isNotEmpty ? description.replaceAll("<br />", "\n").replaceAll("\n\n\n", "\n").replaceAll("\n\n", "\n") : '';
  }
}
