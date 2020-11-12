import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart' as maps;
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/postmates.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/view-models/response/postmates/delivery-response.dart';
import 'package:resold/widgets/loading.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/extensions/string-extension.dart';

class OrderDetails extends StatefulWidget {
  final Order order;
  final Product product;
  final CustomerResponse customer;

  OrderDetails(customer, order, product, {Key key})
      : customer = customer,
        order = order,
        product = product,
        super(key: key);

  @override
  OrderDetailsState createState() => OrderDetailsState(customer, order, product);
}

class OrderDetailsState extends State<OrderDetails> {
  final Order order;
  final Product product;
  final CustomerResponse customer;

  Position currentLocation;
  Future<DeliveryResponse> futureDelivery;
  final Map<String, maps.Marker> markers = {};

  OrderDetailsState(CustomerResponse customer, Order order, Product product)
      : customer = customer,
        order = order,
        product = product;

  @override
  void initState() {
    super.initState();
    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      futureDelivery = Postmates.getDelivery(product.deliveryId);
      if (this.mounted) {
        setState(() {
          currentLocation = location;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
        length: 2,
        child: Scaffold(
            appBar: AppBar(
              title: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Align(
                      alignment: Alignment.centerLeft,
                      child: Container(
                          width: 250,
                          child: Text('Order - ' + product.name, overflow: TextOverflow.ellipsis, style: new TextStyle(color: Colors.white))))
                ],
              ),
              iconTheme: IconThemeData(
                color: Colors.white, //change your color here
              ),
              backgroundColor: const Color(0xff41b8ea),
            ),
            body: FutureBuilder<DeliveryResponse>(
                future: futureDelivery,
                builder: (context, snapshot) {
                  if (snapshot.hasData) {
                    DeliveryResponse delivery = snapshot.data;

                    return Column(children: [
                      Container(
                          height: 400,
                          child: maps.GoogleMap(
                            onMapCreated: (maps.GoogleMapController controller) => this.onMapCreated(controller, delivery),
                            initialCameraPosition: maps.CameraPosition(
                              target: delivery.complete
                                  ? maps.LatLng(delivery.dropoff.location.lat, delivery.dropoff.location.lng)
                                  : maps.LatLng(delivery.pickup.location.lat, delivery.pickup.location.lng),
                              zoom: 13.0,
                            ),
                            markers: markers.values.toSet(),
                          )),
                      Text('Status: ${delivery.status.capitalize()}'),
                      Text('Delivery ETA: ${delivery.dropoff_eta}'),
                      Text('Total: \$${order.total.toString()}')
                    ]);
                  } else {
                    // By default, show a loading spinner.
                    return Center(child: Loading());
                  }
                } // end builder function
                )));
  } // end function build

  Future<void> onMapCreated(maps.GoogleMapController controller, DeliveryResponse delivery) async {
    setState(() {
      markers.clear();

      maps.InfoWindow infoWindow;
      if (product.titleDescription == null) {
        infoWindow = maps.InfoWindow(title: product.name);
      } else {
        infoWindow = maps.InfoWindow(title: product.name, snippet: product.titleDescription);
      }

      final productMarker = maps.Marker(
          markerId: maps.MarkerId(product.name),
          position: maps.LatLng(delivery.pickup.location.lat, delivery.pickup.location.lng),
          infoWindow: infoWindow);

      final String currentLocationTitle = "You";
      final currentLocationMarker = maps.Marker(
        markerId: maps.MarkerId(currentLocationTitle),
        position: maps.LatLng(delivery.dropoff.location.lat, delivery.dropoff.location.lng),
        icon: maps.BitmapDescriptor.defaultMarkerWithHue(198),
        infoWindow: maps.InfoWindow(
          title: currentLocationTitle,
        ),
      );

      markers[product.name] = productMarker;
      markers[currentLocationTitle] = currentLocationMarker;
    });
  } // end function onMapCreated
}
