import 'package:flutter/material.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:resold/constants/url-config.dart';
import 'package:intl/intl.dart';
import 'package:resold/widgets/read-more-text.dart';
import 'package:resold/builders/location-builder.dart';
import 'package:geolocator/geolocator.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

class ProductPage extends StatefulWidget {
  final Product product;
  final Position currentLocation;

  ProductPage(Product product, Position currentLocation, {Key key}) : product = product, currentLocation = currentLocation, super(key: key);

  @override
  ProductPageState createState() => ProductPageState(this.product, this.currentLocation);
}

class ProductPageState extends State<ProductPage> {

  final Product product;
  final Position currentLocation;
  Future<List<String>> futureImages;
  final Map<String, Marker> markers = {};

  ProductPageState(Product product, Position currentLocation) : product = product, currentLocation = currentLocation;

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
                        width: MediaQuery.of(context).size.width,
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
                                  child: FadeInImage(image: NetworkImage(baseImagePath + image), placeholder: AssetImage('assets/images/placeholder-image.png'), fit: BoxFit.cover)
                                );
                              }
                            );
                          }).toList()
                        );
                      }// end if we are displaying one image

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
                                                width: 250,
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
                                                width: 250,
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
                                              Container (
                                                width: 70,
                                                child: Align (
                                                  alignment: Alignment.centerRight,
                                                  child: Text(formatter.format(double.parse(product.price).round()),
                                                    style: new TextStyle(
                                                      fontSize: 14.0,
                                                      fontFamily: 'Roboto',
                                                      fontWeight: FontWeight.bold,
                                                    )
                                                  )
                                                )
                                              ),
                                              Container(
                                                width: 70,
                                                child: Align(
                                                  alignment: Alignment.centerRight,
                                                  child: LocationBuilder.calculateDistance(currentLocation.latitude, currentLocation.longitude, product.latitude, product.longitude)
                                                )
                                              )
                                            ]
                                        )
                                      ]
                                  ),
                                  SizedBox(height: 10),
                                  Container (
                                    width: 500,
                                    child: ReadMoreText (
                                      cleanDescription(product.description),
                                      trimLength: 200,
                                      colorClickableText: const Color(0xff41b8ea),
                                      textAlign: TextAlign.left,
                                    ),
                                  ),
                                  SizedBox(height: 10),
                                  ButtonTheme (
                                      minWidth: 340.0,
                                      height: 70.0,
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
                                        child: Text('Purchase',
                                            style: new TextStyle(
                                                fontSize: 20.0,
                                                fontWeight: FontWeight.bold,
                                                color: Colors.white
                                            )
                                        ),
                                        color: Colors.black,
                                        textColor: Colors.white,
                                    )
                                  ),
                                  SizedBox(height: 5),
                                  ButtonTheme (
                                    minWidth: 340.0,
                                    height: 70.0,
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
                                      child: Text('Send Offer',
                                        style: new TextStyle(
                                            fontSize: 20.0,
                                            fontWeight: FontWeight.bold,
                                            color: Colors.white
                                        )
                                      ),
                                      color: Colors.black,
                                      textColor: Colors.white,
                                    ),
                                  ),
                                  SizedBox(height: 5),
                                  ButtonTheme (
                                    minWidth: 340.0,
                                    height: 70.0,
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
                                      child: Text('Contact Seller',
                                          style: new TextStyle(
                                              fontSize: 20.0,
                                              fontWeight: FontWeight.bold,
                                              color: Colors.white
                                          )
                                      ),
                                      color: Colors.black,
                                      textColor: Colors.white,
                                    )
                                  ),
                                  SizedBox(height: 10),
                                  Container (
                                    height: 500,
                                    child: GoogleMap(
                                      onMapCreated: onMapCreated,
                                      initialCameraPosition: CameraPosition(
                                        target: LatLng(product.latitude, product.longitude),
                                        zoom: 9.0,
                                      ),
                                      markers: markers.values.toSet(),
                                    )
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
                  }// end if we have data
                }// end builder function
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

  Future<void> onMapCreated(GoogleMapController controller) async {
    setState(() {
      markers.clear();
      final productMarker = Marker(
        markerId: MarkerId(product.name),
        position: LatLng(product.latitude, product.longitude),
        infoWindow: InfoWindow(
          title: product.name,
          snippet: product.titleDescription ?? null,
        ),
      );

      final String currentLocationTitle = "You";
      final currentLocationMarker = Marker(
        markerId: MarkerId(currentLocationTitle),
        position: LatLng(currentLocation.latitude, currentLocation.longitude),
        icon: BitmapDescriptor.defaultMarkerWithHue(198),
        infoWindow: InfoWindow(
          title: currentLocationTitle,
        ),
      );

      markers[product.name] = productMarker;
      markers[currentLocationTitle] = currentLocationMarker;
    });
  }

  String cleanDescription (String description) {
    return description.isNotEmpty ? description.replaceAll("<br />", "\n").replaceAll("\n\n\n", "\n").replaceAll("\n\n", "\n").trim() : '';
  }
}
