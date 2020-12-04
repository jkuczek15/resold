import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/enums/user-message-type.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/postmates.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/services/resold.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:resold/constants/url-config.dart';
import 'package:intl/intl.dart';
import 'package:resold/state/actions/delete-product.dart';
import 'package:resold/view-models/request/postmates/delivery-quote-request.dart';
import 'package:resold/view-models/response/postmates/delivery-quote-response.dart';
import 'package:resold/widgets/location/distance.dart';
import 'package:resold/widgets/text/read-more-text.dart';
import 'package:geolocator/geolocator.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:resold/screens/messages/message.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/enums/message-type.dart';
import 'package:resold/widgets/loading.dart';

class ProductPage extends StatefulWidget {
  final CustomerResponse customer;
  final Position currentLocation;
  final Product product;
  final bool fromMessagePage;
  final Function dispatcher;

  ProductPage(
      {CustomerResponse customer,
      Position currentLocation,
      Product product,
      Function dispatcher,
      Key key,
      bool fromMessagePage = false})
      : customer = customer,
        currentLocation = currentLocation,
        product = product,
        dispatcher = dispatcher,
        fromMessagePage = fromMessagePage,
        super(key: key);

  @override
  ProductPageState createState() =>
      ProductPageState(this.customer, this.currentLocation, this.product, this.dispatcher, this.fromMessagePage);
}

class ProductPageState extends State<ProductPage> {
  final CustomerResponse customer;
  final Product product;
  final Function dispatcher;
  final Position currentLocation;
  final bool fromMessagePage;

  Future<List<String>> futureImages;
  final Map<String, Marker> markers = {};
  final offerController = TextEditingController();
  final formKey = GlobalKey<FormState>();
  Future<bool> futureIsMine;
  bool isMine;
  bool deleteCanceled;

  ProductPageState(
      CustomerResponse customer, Position currentLocation, Product product, Function dispatcher, bool fromMessagePage)
      : customer = customer,
        currentLocation = currentLocation,
        product = product,
        dispatcher = dispatcher,
        fromMessagePage = fromMessagePage;

  @override
  void initState() {
    super.initState();
    futureImages = Resold.getProductImages(product.id);
    futureIsMine = ResoldRest.isProductMine(customer.token, product.id);
  } // end function initState

  @override
  Widget build(BuildContext context) {
    var formatter = new NumberFormat("\$###,###", "en_US");

    return Scaffold(
        appBar: AppBar(
            title: Text(product.name, style: new TextStyle(color: Colors.white)),
            backgroundColor: ResoldBlue,
            iconTheme: IconThemeData(
              color: Colors.white, //change your color here
            ),
            actions: <Widget>[
              FutureBuilder<bool>(
                  future: futureIsMine,
                  builder: (context, snapshot) {
                    if (snapshot.hasData) {
                      isMine = snapshot.data;

                      return isMine && product.chargeId == null
                          ? InkWell(
                              child: Icon(MdiIcons.trashCan),
                              onTap: () {
                                return showDialog<void>(
                                  context: context,
                                  barrierDismissible: false,
                                  builder: (BuildContext context) {
                                    return AlertDialog(
                                      title: Text('Delete ${product.name}?'),
                                      content: SingleChildScrollView(
                                        child: ListBody(
                                          children: <Widget>[
                                            Text(
                                              'Are you sure you want to delete this listing?',
                                            ),
                                          ],
                                        ),
                                      ),
                                      actions: <Widget>[
                                        FlatButton(
                                          child: Text(
                                            'Delete',
                                            style: TextStyle(color: ResoldBlue),
                                          ),
                                          onPressed: () async {
                                            Future<bool> complete = Magento.deleteProduct(product.sku);
                                            if (await complete) {
                                              dispatcher(DeleteProductAction(product));
                                              deleteCanceled = false;
                                              Navigator.pop(context);
                                              return showDialog<void>(
                                                  context: context,
                                                  barrierDismissible: false,
                                                  builder: (BuildContext context) {
                                                    return AlertDialog(
                                                        title: Text("Your listing has been deleted."),
                                                        actions: <Widget>[
                                                          FlatButton(
                                                              child: Text(
                                                                'OK',
                                                                style: TextStyle(color: ResoldBlue),
                                                              ),
                                                              onPressed: () {
                                                                Navigator.of(context, rootNavigator: true)
                                                                    .pop('dialog');
                                                              })
                                                        ]);
                                                  });
                                            } // end if deleted product success
                                          },
                                        ),
                                        FlatButton(
                                          child: Text(
                                            'Cancel',
                                            style: TextStyle(color: ResoldBlue),
                                          ),
                                          onPressed: () {
                                            deleteCanceled = true;
                                            Navigator.of(context).pop();
                                          },
                                        ),
                                      ],
                                    );
                                  },
                                ).then((value) {
                                  if (!deleteCanceled) {
                                    Navigator.pop(context);
                                  }
                                });
                              },
                            )
                          : SizedBox();
                    } else {
                      return SizedBox();
                    } // end if we have data
                  } // end future builder
                  )
            ]),
        body: Stack(
          children: [
            FutureBuilder<List<Object>>(
                future: Future.wait([futureImages, futureIsMine]),
                builder: (context, snapshot) {
                  if (snapshot.hasData) {
                    Widget imageElement;
                    List<String> images = snapshot.data[0];

                    if (images.length == 1) {
                      imageElement = CachedNetworkImage(
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
                        imageUrl: baseProductImagePath + images[0],
                        fit: BoxFit.cover,
                      );
                    } else {
                      imageElement = CarouselSlider(
                          options: CarouselOptions(height: 400.0),
                          items: images.map((image) {
                            return Builder(builder: (BuildContext context) {
                              return Container(
                                  width: MediaQuery.of(context).size.width,
                                  margin: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
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
                                    imageUrl: baseProductImagePath + image,
                                    fit: BoxFit.cover,
                                  ));
                            });
                          }).toList());
                    } // end if we are displaying one image

                    return SingleChildScrollView(
                        child: Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        imageElement,
                        Padding(
                            padding: EdgeInsets.symmetric(horizontal: 10.0, vertical: 10.0),
                            child: Column(children: [
                              Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Column(
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
                                        ]),
                                    Column(
                                        mainAxisAlignment: MainAxisAlignment.end,
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Container(
                                              width: 70,
                                              child: Align(
                                                  alignment: Alignment.centerRight,
                                                  child: Text(formatter.format(double.parse(product.price).round()),
                                                      style: new TextStyle(
                                                        fontSize: 14.0,
                                                        fontFamily: 'Roboto',
                                                        fontWeight: FontWeight.bold,
                                                      )))),
                                          Container(
                                              width: 70,
                                              child: Align(
                                                  alignment: Alignment.centerRight,
                                                  child: Distance(
                                                      startLatitude: currentLocation.latitude,
                                                      startLongitude: currentLocation.longitude,
                                                      endLatitude: product.latitude,
                                                      endLongitude: product.longitude)))
                                        ])
                                  ]),
                              SizedBox(height: 10),
                              Container(
                                width: 500,
                                child: ReadMoreText(
                                  cleanDescription(product.description),
                                  trimLength: 200,
                                  colorClickableText: ResoldBlue,
                                  textAlign: TextAlign.left,
                                ),
                              ),
                              SizedBox(height: 10),
                              Column(
                                  children: isMine == true || product.deliveryId != null
                                      ? []
                                      : [
                                          ButtonTheme(
                                              minWidth: 340.0,
                                              height: 70.0,
                                              child: RaisedButton(
                                                shape: RoundedRectangleBorder(
                                                    borderRadius: BorderRadiusDirectional.circular(8)),
                                                onPressed: () async {
                                                  // show a loading indicator
                                                  showDialog(
                                                      context: context,
                                                      builder: (BuildContext context) {
                                                        return Center(child: Loading());
                                                      });

                                                  // get the to customer details
                                                  int toId =
                                                      int.tryParse(await Resold.getCustomerIdByProduct(product.id));
                                                  CustomerResponse toCustomer = await Magento.getCustomerById(toId);
                                                  String chatId = customer.id.toString() + '-' + product.id.toString();

                                                  if (fromMessagePage) {
                                                    // go back
                                                    Navigator.of(context, rootNavigator: true).pop(context);
                                                    Navigator.pop(context);
                                                  } else {
                                                    // push a new message page
                                                    Navigator.push(
                                                        context,
                                                        MaterialPageRoute(
                                                            builder: (context) => MessagePage(
                                                                fromCustomer: customer,
                                                                toCustomer: toCustomer,
                                                                currentLocation: currentLocation,
                                                                product: product,
                                                                chatId: chatId,
                                                                type: UserMessageType.buyer,
                                                                dispatcher: dispatcher)));
                                                    Navigator.of(context, rootNavigator: true).pop('dialog');
                                                  } // end if from message page
                                                },
                                                child: Text('Contact Seller',
                                                    style: new TextStyle(
                                                        fontSize: 20.0,
                                                        fontWeight: FontWeight.bold,
                                                        color: Colors.white)),
                                                color: Colors.black,
                                                textColor: Colors.white,
                                              )),
                                          SizedBox(height: 5),
                                          ButtonTheme(
                                            minWidth: 340.0,
                                            height: 70.0,
                                            child: RaisedButton(
                                              shape: RoundedRectangleBorder(
                                                  borderRadius: BorderRadiusDirectional.circular(8)),
                                              onPressed: () async {
                                                // show a loading indicator
                                                showDialog(
                                                    context: context,
                                                    builder: (BuildContext context) {
                                                      return Center(child: Loading());
                                                    });

                                                // get the to customer details
                                                int toId =
                                                    int.tryParse(await Resold.getCustomerIdByProduct(product.id));
                                                CustomerResponse toCustomer = await Magento.getCustomerById(toId);
                                                String chatId = customer.id.toString() + '-' + product.id.toString();

                                                await showDialog<void>(
                                                    context: context,
                                                    barrierDismissible: false,
                                                    builder: (BuildContext context) {
                                                      return AlertDialog(
                                                        title: Text('Send an offer'),
                                                        content: SingleChildScrollView(
                                                          child: ListBody(
                                                            children: <Widget>[
                                                              Form(
                                                                key: formKey,
                                                                child: TextFormField(
                                                                    controller: offerController,
                                                                    keyboardType: TextInputType.number,
                                                                    decoration: InputDecoration(
                                                                        labelText: 'Enter an offer price *',
                                                                        labelStyle: TextStyle(color: ResoldBlue),
                                                                        enabledBorder: UnderlineInputBorder(
                                                                            borderSide: BorderSide(
                                                                                color: ResoldBlue, width: 1.5)),
                                                                        focusedBorder: UnderlineInputBorder(
                                                                            borderSide: BorderSide(
                                                                                color: ResoldBlue, width: 1.5)),
                                                                        border: UnderlineInputBorder(
                                                                            borderSide: BorderSide(
                                                                                color: ResoldBlue, width: 1.5))),
                                                                    validator: (value) {
                                                                      int offerPrice = int.tryParse(value);
                                                                      if (value.isEmpty || offerPrice < 1) {
                                                                        return 'Please enter a valid offer.';
                                                                      }
                                                                      return null;
                                                                    },
                                                                    style: TextStyle(color: Colors.black)),
                                                              )
                                                            ],
                                                          ),
                                                        ),
                                                        actions: <Widget>[
                                                          FlatButton(
                                                            child: Text(
                                                              'OK',
                                                              style: TextStyle(color: ResoldBlue),
                                                            ),
                                                            onPressed: () async {
                                                              if (formKey.currentState.validate()) {
                                                                Navigator.of(context, rootNavigator: true)
                                                                    .pop('dialog');

                                                                // send a Firebase message
                                                                await ResoldFirebase.sendProductMessage(
                                                                    chatId,
                                                                    customer.id,
                                                                    toCustomer.id,
                                                                    product,
                                                                    customer.id.toString() + '|' + offerController.text,
                                                                    MessageType.offer,
                                                                    toId == customer.id,
                                                                    firstMessage: true);

                                                                // send a notification message
                                                                await ResoldRest.sendNotificationMessage(
                                                                    customer.token,
                                                                    toCustomer.deviceToken,
                                                                    product.name,
                                                                    'Offer received for \$${offerController.text}.',
                                                                    product.thumbnail);

                                                                if (fromMessagePage) {
                                                                  // go back
                                                                  Navigator.of(context, rootNavigator: true)
                                                                      .pop(context);
                                                                  Navigator.pop(context);
                                                                } else {
                                                                  // push a new message page
                                                                  offerController.value = TextEditingValue();
                                                                  Navigator.of(context, rootNavigator: true)
                                                                      .pop('dialog');
                                                                  Navigator.push(
                                                                      context,
                                                                      MaterialPageRoute(
                                                                          builder: (context) => MessagePage(
                                                                              fromCustomer: customer,
                                                                              toCustomer: toCustomer,
                                                                              currentLocation: currentLocation,
                                                                              product: product,
                                                                              chatId: chatId,
                                                                              type: UserMessageType.buyer,
                                                                              dispatcher: dispatcher)));
                                                                } // end if not from message page
                                                              } // end if valid verification code
                                                            },
                                                          ),
                                                          FlatButton(
                                                            child: Text(
                                                              'Cancel',
                                                              style: TextStyle(color: ResoldBlue),
                                                            ),
                                                            onPressed: () {
                                                              offerController.value = TextEditingValue();
                                                              Navigator.of(context, rootNavigator: true).pop('dialog');
                                                              Navigator.of(context, rootNavigator: true).pop('dialog');
                                                            },
                                                          ),
                                                        ],
                                                      );
                                                    });
                                              },
                                              child: Text('Send Offer',
                                                  style: new TextStyle(
                                                      fontSize: 20.0,
                                                      fontWeight: FontWeight.bold,
                                                      color: Colors.white)),
                                              color: Colors.black,
                                              textColor: Colors.white,
                                            ),
                                          ),
                                          SizedBox(height: 5),
                                          ButtonTheme(
                                              minWidth: 340.0,
                                              height: 70.0,
                                              child: RaisedButton(
                                                shape: RoundedRectangleBorder(
                                                    borderRadius: BorderRadiusDirectional.circular(8)),
                                                onPressed: () async {
                                                  // show a loading indicator
                                                  showDialog(
                                                      context: context,
                                                      builder: (BuildContext context) {
                                                        return Center(child: Loading());
                                                      });

                                                  // get the to customer details
                                                  int toId =
                                                      int.tryParse(await Resold.getCustomerIdByProduct(product.id));
                                                  CustomerResponse toCustomer = await Magento.getCustomerById(toId);
                                                  String chatId = customer.id.toString() + '-' + product.id.toString();

                                                  // get a Postmates delivery quote
                                                  DateTime now = DateTime.now();

                                                  var pickupDeadline = now.add(Duration(minutes: 30));
                                                  var dropoffDeadline = pickupDeadline.add(Duration(hours: 2));

                                                  // create a Postmates delivery quote
                                                  DeliveryQuoteResponse quote = await Postmates.createDeliveryQuote(
                                                      DeliveryQuoteRequest(
                                                          pickup_address: customer.addresses.first.toString(),
                                                          pickup_ready_dt: now.toUtc().toIso8601String(),
                                                          pickup_deadline_dt: pickupDeadline.toUtc().toIso8601String(),
                                                          dropoff_address: toCustomer.addresses.first.toString(),
                                                          dropoff_ready_dt: now.toUtc().toIso8601String(),
                                                          dropoff_deadline_dt:
                                                              dropoffDeadline.toUtc().toIso8601String()));

                                                  // prepare message content for delivery request
                                                  String content = quote.id +
                                                      '|' +
                                                      quote.fee.toString() +
                                                      '|' +
                                                      quote.pickup_duration.toString() +
                                                      '|' +
                                                      quote.duration.toString();

                                                  // send a Firebase message
                                                  await ResoldFirebase.sendProductMessage(
                                                      chatId,
                                                      customer.id,
                                                      toCustomer.id,
                                                      product,
                                                      content,
                                                      MessageType.deliveryQuote,
                                                      toId == customer.id,
                                                      firstMessage: true);

                                                  // send a notification message
                                                  await ResoldRest.sendNotificationMessage(
                                                      customer.token,
                                                      toCustomer.deviceToken,
                                                      product.name,
                                                      '${customer.fullName} has requested a delivery.',
                                                      product.thumbnail);

                                                  if (fromMessagePage) {
                                                    // go back
                                                    Navigator.of(context, rootNavigator: true).pop(context);
                                                    Navigator.pop(context);
                                                  } else {
                                                    // push a new message page
                                                    Navigator.push(
                                                        context,
                                                        MaterialPageRoute(
                                                            builder: (context) => MessagePage(
                                                                fromCustomer: customer,
                                                                toCustomer: toCustomer,
                                                                currentLocation: currentLocation,
                                                                product: product,
                                                                chatId: chatId,
                                                                type: UserMessageType.buyer,
                                                                dispatcher: dispatcher)));
                                                    Navigator.of(context, rootNavigator: true).pop('dialog');
                                                  } // end if not from message page
                                                },
                                                child: Text('Request Delivery',
                                                    style: new TextStyle(
                                                        fontSize: 20.0,
                                                        fontWeight: FontWeight.bold,
                                                        color: Colors.white)),
                                                color: Colors.black,
                                                textColor: Colors.white,
                                              )),
                                        ]),
                              SizedBox(height: 10),
                              Container(
                                  height: 500,
                                  child: GoogleMap(
                                    myLocationEnabled: true,
                                    mapToolbarEnabled: false,
                                    onMapCreated: onMapCreated,
                                    initialCameraPosition: CameraPosition(
                                      target: LatLng(product.latitude, product.longitude),
                                      zoom: 9.0,
                                    ),
                                    markers: markers.values.toSet(),
                                  ))
                            ]))
                      ],
                    ));
                  } else {
                    return Column(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [Center(child: Loading())]);
                  } // end if we have data
                } // end builder function
                )
          ],
        ));
  } // end function build

  Future<void> onMapCreated(GoogleMapController controller) async {
    setState(() {
      markers.clear();

      final productMarker = Marker(
          markerId: MarkerId(product.name),
          position: LatLng(product.latitude, product.longitude),
          icon: BitmapDescriptor.defaultMarkerWithHue(198),
          infoWindow: InfoWindow(title: product.name));

      markers[product.name] = productMarker;
    });
  } // end function onMapCreated

  String cleanDescription(String description) {
    return description.isNotEmpty
        ? description.replaceAll("<br />", "\n").replaceAll("\n\n\n", "\n").replaceAll("\n\n", "\n").trim()
        : '';
  } // end function cleanDescription
}
