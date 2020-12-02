import 'dart:typed_data';
import 'dart:ui' as ui;
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart' as maps;
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/environment.dart';
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/postmates.dart';
import 'package:resold/view-models/response/postmates/delivery-response.dart';
import 'package:resold/widgets/loading.dart';
import 'package:geolocator/geolocator.dart';

class OrderDetails extends StatefulWidget {
  final Order order;
  final Product product;
  final bool isSeller;

  OrderDetails({Order order, Product product, Key key, isSeller = false})
      : order = order,
        product = product,
        isSeller = isSeller,
        super(key: key);

  @override
  OrderDetailsState createState() => OrderDetailsState(order, product, isSeller);
}

class OrderDetailsState extends State<OrderDetails> {
  final Order order;
  final Product product;
  final bool isSeller;

  Future<DeliveryResponse> futureDelivery;
  final Map<String, maps.Marker> markers = {};

  // variables used to store polyline points for Google Maps
  PolylinePoints polylinePoints;
  List<maps.LatLng> polylineCoordinates = [];
  Map<maps.PolylineId, maps.Polyline> polylines = {};

  // step counter
  int currentStep = 0;
  List<Step> steps;

  maps.BitmapDescriptor carLocationIcon = maps.BitmapDescriptor.defaultMarker;

  OrderDetailsState(Order order, Product product, bool isSeller)
      : order = order,
        product = product,
        isSeller = isSeller;

  @override
  void initState() {
    super.initState();
    futureDelivery = Postmates.getDelivery(product.deliveryId);
    setCustomMapPin();
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
                          child: Text(product.name,
                              overflow: TextOverflow.ellipsis, style: new TextStyle(color: Colors.white))))
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
                    DateTime now = DateTime.now();
                    Duration difference;

                    // setup the steps
                    setupSteps(delivery);

                    if (this.currentStep == 0 && isSeller) {
                      if (delivery.pickup_eta != null) {
                        difference = delivery.pickup_eta.difference(now);
                      } // end if pickup eta not null
                    } else if (this.currentStep == 1) {
                      if (delivery.dropoff_eta != null) {
                        difference = delivery.dropoff_eta.difference(now);
                      } // end if dropoff eta not null
                    } // end if pickup and is seller

                    double total = order.total;
                    double fee = delivery.fee.toDouble() / 100;
                    if (!isSeller) {
                      total += fee;
                    } // end if buyer

                    return SingleChildScrollView(
                        child: Column(children: [
                      Container(
                          height: 380,
                          child: FutureBuilder(
                              future: this.generateMarkers(delivery),
                              initialData: Set.of(<Marker>[]),
                              builder: (context, snapshot) => maps.GoogleMap(
                                  myLocationEnabled: delivery.status == 'delivered',
                                  onMapCreated: (maps.GoogleMapController controller) =>
                                      this.onMapCreated(controller, delivery),
                                  mapType: maps.MapType.normal,
                                  initialCameraPosition: maps.CameraPosition(
                                    target: delivery.complete
                                        ? maps.LatLng(delivery.dropoff.location.lat, delivery.dropoff.location.lng)
                                        : maps.LatLng(delivery.pickup.location.lat, delivery.pickup.location.lng),
                                    zoom: 13.0,
                                  ),
                                  markers: snapshot.data,
                                  gestureRecognizers: Set()
                                    ..add(Factory<PanGestureRecognizer>(() => PanGestureRecognizer())),
                                  polylines: Set<maps.Polyline>.of(polylines.values)))),
                      Card(
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        ConstrainedBox(
                          constraints: BoxConstraints.tightFor(height: 125, width: 400),
                          child: Stepper(
                              currentStep: this.currentStep,
                              steps: steps,
                              physics: NeverScrollableScrollPhysics(),
                              type: StepperType.horizontal,
                              controlsBuilder: getStepControls),
                        ),
                        difference != null
                            ? Padding(
                                padding: EdgeInsets.fromLTRB(23, 3, 0, 0),
                                child: Text('Arriving in ${difference.inMinutes} minutes'))
                            : SizedBox(),
                        Divider(
                          color: Colors.grey.shade400,
                          height: 20,
                          thickness: 2,
                          indent: 23,
                          endIndent: 15,
                        ),
                        Padding(
                          padding: EdgeInsets.fromLTRB(25, 0, 0, 0),
                          child: Align(
                            alignment: Alignment.centerLeft,
                            child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                              Column(
                                  mainAxisAlignment: MainAxisAlignment.start,
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      width: MediaQuery.of(context).size.width - 40,
                                      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                                        Text(product.description),
                                        Padding(
                                            padding: EdgeInsets.fromLTRB(10, 10, 10, 10),
                                            child: Container(
                                              height: 90,
                                              width: 90,
                                              child: CachedNetworkImage(
                                                placeholder: (context, url) => Container(
                                                  child: Loading(),
                                                  width: MediaQuery.of(context).size.width,
                                                  padding: EdgeInsets.all(70.0),
                                                  decoration: BoxDecoration(
                                                    color: Colors.blueGrey,
                                                    borderRadius: BorderRadius.all(
                                                      Radius.circular(8.0),
                                                    ),
                                                  ),
                                                ),
                                                errorWidget: (context, url, error) => Material(
                                                  child: Image.asset(
                                                    'assets/images/placeholder-image.png',
                                                    width: 200.0,
                                                    height: 200.0,
                                                    fit: BoxFit.cover,
                                                  ),
                                                  borderRadius: BorderRadius.all(
                                                    Radius.circular(8.0),
                                                  ),
                                                  clipBehavior: Clip.hardEdge,
                                                ),
                                                imageUrl: baseProductImagePath + product.thumbnail,
                                                fit: BoxFit.cover,
                                              ),
                                            ))
                                      ]),
                                    ),
                                    Row(children: [
                                      Container(
                                          width: 345,
                                          child: Divider(
                                            color: Colors.grey.shade400,
                                            height: 20,
                                            thickness: 2,
                                            indent: 0,
                                            endIndent: 0,
                                          ))
                                    ]),
                                    Row(children: [
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: isSeller
                                            ? [Text('Your Profit:')]
                                            : [Text('Subtotal:'), Text('Delivery Fee:'), Text('Total:')],
                                      ),
                                      SizedBox(width: 25),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: isSeller
                                            ? [Text('\$${order.total.toStringAsFixed(2)}')]
                                            : [
                                                Text('\$${order.total.toStringAsFixed(2)}'),
                                                Text('\$${fee.toStringAsFixed(2)}'),
                                                Text('\$${total.toStringAsFixed(2)}')
                                              ],
                                      )
                                    ]),
                                    SizedBox(height: 20)
                                  ]),
                            ]),
                          ),
                        ),
                      ]))
                    ]));
                  } else {
                    // By default, show a loading spinner.
                    return Center(child: Loading());
                  }
                } // end builder function
                )));
  } // end function build

  Future<Set<Marker>> generateMarkers(DeliveryResponse delivery) async {
    List<Marker> markers = <Marker>[];
    markers.clear();
    maps.InfoWindow infoWindow;

    infoWindow = maps.InfoWindow(title: product.name);

    if (delivery.status != 'delivered') {
      // add the current location marker
      final String currentLocationTitle = 'You';
      final currentLocationMarker = maps.Marker(
        markerId: maps.MarkerId(currentLocationTitle),
        position: maps.LatLng(delivery.dropoff.location.lat, delivery.dropoff.location.lng),
        icon: maps.BitmapDescriptor.defaultMarkerWithHue(198),
        infoWindow: maps.InfoWindow(
          title: currentLocationTitle,
        ),
      );

      markers.add(currentLocationMarker);
    } // end if delivery not yet complete

    if (delivery.status != 'delivered') {
      // add the product marker
      final productMarker = maps.Marker(
          markerId: maps.MarkerId(product.name),
          position: maps.LatLng(delivery.pickup.location.lat, delivery.pickup.location.lng),
          infoWindow: infoWindow);
      markers.add(productMarker);
    } // end if not delivered

    if (delivery.courier != null) {
      // place a marker on the map for the delivery driver
      final String courierTitle = delivery.courier.name;
      final courierMarker = maps.Marker(
        markerId: maps.MarkerId(courierTitle),
        position: maps.LatLng(delivery.courier.location.lat, delivery.courier.location.lng),
        icon: carLocationIcon,
        infoWindow: maps.InfoWindow(
          title: courierTitle,
        ),
      );
      markers.add(courierMarker);
    } // end if we have a delivery driver

    return markers.toSet();
  } // end function generateMarkers

  Future<void> onMapCreated(maps.GoogleMapController controller, DeliveryResponse delivery) async {
    if (delivery.status != 'delivered' && order.status == 'delivery_in_progress') {
      await createPolylines(Position(latitude: delivery.pickup.location.lat, longitude: delivery.pickup.location.lng),
          Position(latitude: delivery.dropoff.location.lat, longitude: delivery.dropoff.location.lng));
    } // end if not delivered
  } // end function onMapCreated

  // Create the polylines for showing the route between two places
  Future createPolylines(Position start, Position destination) async {
    // Initializing PolylinePoints
    polylinePoints = PolylinePoints();

    // Generating the list of coordinates to be used for
    // drawing the polylines
    PolylineResult result = await polylinePoints.getRouteBetweenCoordinates(
      env.googleMapsApiKey, // Google Maps API Key
      PointLatLng(start.latitude, start.longitude),
      PointLatLng(destination.latitude, destination.longitude),
      travelMode: TravelMode.transit,
    );

    // Adding the coordinates to the list
    if (result.points.isNotEmpty) {
      result.points.forEach((PointLatLng point) {
        polylineCoordinates.add(maps.LatLng(point.latitude, point.longitude));
      });
    }

    // Defining an ID
    maps.PolylineId id = maps.PolylineId('poly');

    // Initializing Polyline
    maps.Polyline polyline = maps.Polyline(
      polylineId: id,
      color: ResoldBlue,
      points: polylineCoordinates,
      width: 3,
    );

    setState(() {
      // Adding the polyline to the map
      polylines[id] = polyline;
    });
  } // end function createPolylines

  Widget getStepControls(BuildContext context, {void onStepCancel, void onStepContinue}) {
    return SizedBox(height: 0);
  } // end function getStepControls

  void setupSteps(DeliveryResponse delivery) {
    bool driverUnassigned = delivery.status == 'pending';
    bool pickupInProgress = delivery.status == 'pickup';
    bool deliveryInProgress =
        delivery.status == 'pickup_complete' || delivery.status == 'dropoff' || delivery.status == 'ongoing';
    bool delivered = delivery.status == 'delivered';

    if (pickupInProgress) {
      this.currentStep = 0;
    } else if (deliveryInProgress) {
      this.currentStep = 1;
    } else if (delivered) {
      this.currentStep = 2;
    } // end if pickup in progress

    Widget driverWidget = SizedBox();
    if (delivery.courier != null) {
      driverWidget =
          Column(children: [Text('Driver: ${delivery.courier.name}'), Text('${delivery.courier.vehicle_type}')]);
    } // end if we have a delivery driver

    steps = [
      Step(
        title: Text('Pickup'),
        content: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: driverUnassigned
                ? [Text('Driver hasn\'t been assigned.')]
                : [Text('Driver is on the way to pickup your item.'), driverWidget]),
        state: pickupInProgress ? StepState.indexed : StepState.complete,
        isActive: pickupInProgress,
      ),
      Step(
        title: Text('On the way'),
        content: Column(
            mainAxisAlignment: MainAxisAlignment.start,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [Text('Driver is on the way to deliver your item.'), driverWidget]),
        state: (pickupInProgress || deliveryInProgress) ? StepState.indexed : StepState.complete,
        isActive: deliveryInProgress,
      ),
      Step(
        title: Text('Delivered'),
        content: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text('Delivery was completed.'),
        ]),
        state: delivered ? StepState.complete : StepState.indexed,
        isActive: delivered,
      ),
    ];
  } // end function setupSteps

  Future<Uint8List> getBytesFromAsset(String path, int width) async {
    ByteData data = await rootBundle.load(path);
    ui.Codec codec = await ui.instantiateImageCodec(data.buffer.asUint8List(), targetWidth: width);
    ui.FrameInfo fi = await codec.getNextFrame();
    return (await fi.image.toByteData(format: ui.ImageByteFormat.png)).buffer.asUint8List();
  } // end function getBytesFromAsset

  void setCustomMapPin() async {
    final Uint8List markerIcon = await getBytesFromAsset('assets/images/car-location-icon.png', 100);
    carLocationIcon = BitmapDescriptor.fromBytes(markerIcon);
  } // end function setCustomMapPin
}
