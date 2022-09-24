<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('files/download/{file}','FileController@download');
Route::get('files/show/{file}','FileController@download');


Route::group([
    'namespace' =>  'Auth',
    'prefix'    =>  'auth'
], function(){
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout');
    Route::post('register', 'LoginController@signup');
    Route::group(['middleware' => 'auth:api'], function()
    {
        // Route::get('verifyUser','UserController@datosUsuarioToken');
        // Route::post('resultIdAnalizer','FinancieraController@analisisIdAnalizer');
    });
});
Route::get('userToken/{id}','FinancieraController@showToken');
Route::post('createData', 'FinancieraController@createData');
Route::post('createContraOferta', 'FinancieraController@createContraOferta');
 Route::get('file/export','FinancieraController@export');
 Route::post('recuperarPassword','UserController@sendPassword');
 Route::post('actualizarPassword','UserController@actualizarPassword');
 Route::post('storeUserBasica','UserController@storeUserBasica');
 Route::post('addUserRefer','UserController@addUserRefer');
 Route::post('emailReferido','UserController@emailReferido');
 Route::post('consultaUsuario','UserController@consultaUsuario');
 Route::post('consultaSolicitud','UserController@consultaSolicitud');
 Route::get('blacklist','UserController@getListanegra');
 Route::post('listanegra','UserController@blackList');
 Route::post('informacionCompleta','UserController@informacionCompleta');
 Route::post('informacionCompletaPorEmail','UserController@informacionCompletaPorEmail');
 Route::post('getAPI','Loandisk_apiController@get');
 Route::post('postAPI','Loandisk_apiController@post');
 Route::post('getLoans','UserController@creditos');
 Route::post('invitar','UserController@invitar');
 Route::post('estadisticasReferido','UserController@estadisticasReferido');
 Route::post('detallePagoReferidor','UserController@detallePagoReferidor');
 Route::post('campana','UserController@enviarCampana');
 Route::post('desactivarEnvio','UserController@desactivarEnvio');
 Route::post('actualizarFirebase','UserController@actualizarFirebase');
 Route::get('notifications','NotificacionesController@all');

 Route::post('consultaContraoferta','ContraOfertaController@contraOfertaPendiente');
Route::group([
    'prefix'        => 'blogs',
], function () {
    Route::get('get','BlogController@getAll');
    Route::post('get','BlogController@all');
    Route::post('getBlog','BlogController@getAllBlog');
    Route::post('getDetalleBlog','BlogController@getDetalleBlog');
    Route::post('create','BlogController@create');
    Route::post('update','BlogController@update');
    Route::post('delete','BlogController@delete');
});

Route::group([
    'prefix'        => 'evento',
], function () {
    Route::get('get','EventosController@getAll');
    Route::post('create','EventosController@create');
    Route::post('update','EventosController@update');
    Route::post('delete','EventosController@delete');
});
Route::group([
    'prefix'        => 'correos',
], function () {
    Route::get('get','CorreosController@getAll');
    // Route::post('get','CorreosController@all');
    Route::post('getCorreos','CorreosController@getAllCorreos');
    Route::post('create','CorreosController@create');
    Route::post('update','CorreosController@update');
    Route::post('cambioEstatus','CorreosController@cambioEstatus');
    Route::post('delete','CorreosController@delete');
});
Route::group([
    'prefix'        => 'preguntas',
], function () {
    Route::get('get','PreguntasController@getAll');
    Route::post('get','PreguntasController@all');
    Route::post('getPregunta','PreguntasController@getAllBlog');
    Route::post('create','PreguntasController@create');
    Route::post('update','PreguntasController@update');
    Route::post('delete','PreguntasController@delete');
});
Route::group([
    'prefix'        => 'parascore',
], function () {
    Route::get('get','ParascoreController@getAll');
    Route::post('getscore','ParascoreController@getAllBlog');
    Route::post('create','ParascoreController@create');
    Route::post('update','ParascoreController@update');
    Route::post('delete','ParascoreController@delete');
});
Route::group([
    'prefix'        => 'atributos',
], function () {
    Route::get('get','AtributosController@getAll');
    Route::post('getAtributo','AtributosController@getAllBlog');
    Route::post('create','AtributosController@create');
    Route::post('update','AtributosController@update');
    Route::post('delete','AtributosController@delete');
});
Route::group([
    'prefix'        => 'filtrado',
], function () {
    Route::get('get','FiltradoController@getAll');
    Route::post('getFiltro','FiltradoController@getAllBlog');
    Route::post('create','FiltradoController@create');
    Route::post('update','FiltradoController@update');
    Route::post('delete','FiltradoController@delete');
});
Route::group([
    'prefix'        => 'variables',
], function () {
    Route::post('get','VariablesController@getAll');
    Route::post('getVariable','VariablesController@getAllBlog');
    Route::post('create','VariablesController@create');
    Route::post('update','VariablesController@update');
    Route::post('delete','VariablesController@delete');
});
Route::group([
    'prefix'        => 'testimonio',
], function () {
    Route::get('get','TestimonioController@getAll');
    Route::post('get','TestimonioController@all');
    Route::post('getTestimonio','TestimonioController@getAllBlog');
    Route::post('create','TestimonioController@create');
    Route::post('update','TestimonioController@update');
    Route::post('delete','TestimonioController@delete');
});
Route::group([
    'prefix'        => 'comentarios',
], function () {
    Route::get('get','ComentariosController@getAll');
    Route::post('getEvaluacion','ComentariosController@getAllEvaluacion');
    Route::post('solicitud','ComentariosController@getSolicitud');
    Route::post('getComentario','ComentariosController@getAllBlog');
    Route::post('create','ComentariosController@create');
    Route::post('createSolicitud','ComentariosController@createSolicitud');
    Route::post('update','ComentariosController@update');
    Route::post('delete','ComentariosController@delete');
});
Route::group([
    'prefix'        => 'contacto',
], function () {
    Route::get('get','ContactoController@getAll');
    Route::post('create','ContactoController@create');
    Route::post('update','ContactoController@update');
    Route::post('show','ContactoController@show');
    Route::post('delete','ContactoController@delete');
});
Route::group([
    'prefix'        => 'basica',
], function () {
    Route::post('get','BasicaController@getAll');
    Route::post('create','BasicaController@create');
    Route::post('update','BasicaController@update');
    Route::post('delete','BasicaController@delete');
    Route::post('informacionCompletaPorEmail','BasicaController@informacionCompletaPorEmail');
});
Route::group([
    'prefix'        => 'referencias',
], function () {
    Route::post('get','ReferenciasController@getAll');
    Route::post('create','ReferenciasController@create');
    Route::post('update','ReferenciasController@update');
    Route::post('delete','ReferenciasController@delete');
});

Route::group([
    'prefix'        => 'contact-history',
], function () {
    Route::post('store','ContactHistoryController@store');
});

Route::group([
    'prefix'        => 'record-history',
], function () {
    Route::get('get-uuid','RecordHistoryController@getUuid');
    Route::post('verifyTodayVisit','RecordHistoryController@verifyTodayVisit');
    Route::put('update/{uuid}','RecordHistoryController@update');
});

Route::group([
    'prefix'        => 'financiera',
], function () {
    Route::post('get','FinancieraController@getAll');
    Route::post('create','FinancieraController@create');
    Route::post('solicitud','FinancieraController@createPrestamo');
    Route::post('update','FinancieraController@update');
    Route::post('delete','FinancieraController@delete');
    Route::post('analisis','FinancieraController@empezarAnalisis');
    Route::get('obtenerCreditos','FinancieraController@getAllCreditos');
    Route::get('obtenerCreditosAbiertos','FinancieraController@getAllCreditosAbiertos');
    Route::get('obtenerCreditosCerrados','FinancieraController@getAllCreditosCerrados');
    Route::get('obtenerCreditosAbiertosUsuario','FinancieraController@getAllCreditosAbiertosUsuario');
    Route::get('obtenerCreditosAbiertosUsuarioPor','FinancieraController@getAllCreditosAbiertosPorUsuario');
    Route::get('obtenerCreditosMorosos','FinancieraController@getCreditosMorosos');
    Route::get('generateDescuentoLibranzaDocument','FinancieraController@generateDescuentoLibranzaDocument');
    Route::get('generateControlBancoDocument','FinancieraController@generateControlBancoDocument');
    Route::get('generateAvisoPrejuridicoDocument','FinancieraController@generateAvisoPrejuridicoDocument');
    Route::get('obtenerCreditosPagadosUsuario','FinancieraController@getAllCreditosPagadosPorUsuario');
    Route::post('obtenerDetalleCA','FinancieraController@getDetalleCA');
    Route::post('obtenerDetalleCC','FinancieraController@getDetalleCC');
    Route::post('desembolso','FinancieraController@desembolso');
    Route::post('getDesembolso','FinancieraController@getDesembolso');
    Route::post('realizarPago','FinancieraController@realizarPago');
    Route::post('realizarPagoParcial','FinancieraController@realizarPagoParcial');
    Route::post('editarPagoIntereses','FinancieraController@editarPagoIntereses');
    Route::post('modificarContratos','FinancieraController@modificarContratos');
    Route::post('modificarEstatusIntereses','FinancieraController@modificarEstatusIntereses');
    Route::post('cargarFactura','FinancieraController@cargarFactura');
    Route::get('exportExcelCA','FinancieraController@exportExcelCA');
    Route::get('exportExcelCC','FinancieraController@exportExcelCC');
    Route::get('getAllCupones','FinancieraController@getAllCupones');
    Route::post('createCupon','FinancieraController@createCupon');
    Route::post('obtenerCupon','FinancieraController@obtenerCupon');
    Route::post('deleteCupon','FinancieraController@deleteCupon');
    Route::post('obtenerCuponPreview','FinancieraController@obtenerCuponPreview');
    Route::get('actualizar_desembolso','FinancieraController@actualizar_desembolso');
    Route::post('actualizar_desembolso_uno','FinancieraController@actualizar_desembolso_uno');
    Route::post('solicitarFirma','FinancieraController@solicitarFirma');
    Route::post('analisisEmail','FinancieraController@controlMail');
    Route::post('analisisScrapping','FinancieraController@analisisWebScrapping');
    Route::post('analisisKonivin','FinancieraController@analisisKonivin');
});
Route::group([
    'prefix'        => 'contraOferta',
], function () {
    Route::get('all','ContraOfertaController@all');
    Route::get('allEstatus','ContraOfertaController@allEstatus');
    Route::get('allUser','ContraOfertaController@allUser');
    Route::get('aprobados','ContraOfertaController@aprobados');
    Route::get('preaprobados','ContraOfertaController@preaprobados');
    Route::post('get','ContraOfertaController@get');
    Route::post('actualizaEstatus','ContraOfertaController@updateEstatus');
    Route::post('delete','ContraOfertaController@delete');
});
Route::group([
    'prefix'        => 'modulos',
], function () {
    Route::get('all','ModulosController@all');
    Route::get('allAdmin','ModulosController@allAdmin');
});
Route::group([
    'prefix'        => 'configCalculadora',
], function () {
    Route::get('all','ConfigCalculadoraController@all');
    Route::post('get','ConfigCalculadoraController@get');
    Route::post('getTipo','ConfigCalculadoraController@getTipo');
    Route::post('create','ConfigCalculadoraController@store');
    Route::post('update','ConfigCalculadoraController@update');
    Route::post('delete','ConfigCalculadoraController@delete');
});
Route::group([
    'prefix'        => 'configContraOferta',
], function () {
    Route::get('all','ConfigContraOfertaController@all');
    Route::post('get','ConfigContraOfertaController@get');
    Route::post('getTipo','ConfigContraOfertaController@getTipo');
    Route::post('create','ConfigContraOfertaController@store');
    Route::post('update','ConfigContraOfertaController@update');
    Route::post('delete','ConfigContraOfertaController@delete');
});


Route::group([
    'prefix'        => 'evaluacion',
], function () {
    Route::get('all','EvaluacionController@all');
    Route::post('get','EvaluacionController@get');
    Route::post('getTipo','EvaluacionController@getTipo');
    Route::post('create','EvaluacionController@store');
    Route::post('update','EvaluacionController@update');
    Route::post('updateSelfie','EvaluacionController@updateSelfie');
    Route::post('updateBalance','EvaluacionController@updateBalance');
    Route::post('updateIdentidad','EvaluacionController@updateIdentidad');
    Route::post('updateAdicionales','EvaluacionController@updateAdicionales');
    Route::post('updateLlamada','EvaluacionController@updateLlamada');
    Route::post('updateDataCredito','EvaluacionController@updateDataCredito');
    Route::post('showSolicitud','EvaluacionController@showSolicitud');
    Route::post('delete','EvaluacionController@delete');
    Route::post('comentarioSelfie','EvaluacionController@comentarioSelfie');
    Route::post('comentarioIdentidad','EvaluacionController@comentarioIdentidad');
    Route::post('comentarioAdicional','EvaluacionController@comentarioAdicional');
    Route::post('comentarioLamada','EvaluacionController@comentarioLamada');
    Route::post('solicitarSelfie','EvaluacionController@solicitarSelfie');
    Route::post('solicitarAdicional','EvaluacionController@solicitarAdicional');
    Route::post('estatusSolicitudes','EvaluacionController@estadosSolicitudes');
    Route::post('solicitarVerificacion','EvaluacionController@solicitarVerificacion');
    Route::post('solicitarExtracto','EvaluacionController@solicitarExtracto');
    Route::post('solicitarCertificado','EvaluacionController@solicitarCertificado');
    Route::post('solicitarDesprendible','EvaluacionController@solicitarDesprendible');
    Route::post('solicitarCertificadoLaboral','EvaluacionController@solicitarCertificadoLaboral');
    Route::post('obtenerDatos','EvaluacionController@obtenerDatosKonivin');
    Route::post('updateCalculos','EvaluacionController@updateCalculos');
    Route::get('exportExcel','EvaluacionController@exportExcel');
    Route::post('updateExtractoBancario','EvaluacionController@updateExtractoBancario');
    Route::post('estadisticas','EvaluacionController@estadisticas');
    Route::post('verifyStatusEvaluation','EvaluacionController@isEvaluationApproved');
    Route::post('manualApproveEmail','EvaluacionController@manualApproveEmail');
    Route::post('manualApproveVerifiquese','EvaluacionController@manualApproveVerifiquese');
    Route::group(['middleware' => 'auth:api'], function()
    {
        Route::get('verifyUser','UserController@datosUsuarioToken');
        Route::post('resultIdAnalizer','FinancieraController@analisisIdAnalizer');
    });
    // Route::get('verifyUser/{token}','UserController@datosUsuarioToken');
    // Route::post('resultIdAnalizer','FinancieraController@analisisIdAnalizer');
});
Route::group([
    'prefix'        => 'adicional',
], function () {
    Route::post('get','AdicionalController@getAll');
    Route::post('create','AdicionalController@create');
    Route::post('update','AdicionalController@update');
    Route::post('delete','AdicionalController@delete');
    Route::post('file/export','AdicionalController@export');
});
Route::group([
    'prefix'        => 'users',
], function () {
    Route::post('create','UserController@store');
    Route::post('update','UserController@update');
    Route::post('delete','UserController@delete');
    Route::post('updateUser', 'UserController@updateUser');
    Route::get('allAdmin','UserController@allAdmin');
    Route::get('allReferidos','UserController@allReferidos');
    Route::get('misReferidos/{id}','UserController@misReferidos');
    Route::get('export/{id}','UserController@export');
    Route::get('exportPDF/{id}','UserController@exportPDF');
    Route::get('exportConsignacion/{id}','UserController@exportConsignacion');
    Route::get('exportExcel','UserController@exportExcel');
    Route::get('download/{id}/{doc}','UserController@download');
    Route::get('downloadFactura/{id}','UserController@downloadFactura');
    Route::get('exportExcelAdmin','UserController@exportExcelAdmin');
    Route::post('firmar/{tocken}','UserController@firmar');
    Route::get('consultaFirmar/{tocken}','UserController@consultaFirmar');
    Route::post('consultarCodigoActivo','UserController@consultarCodigoActivo');
    Route::post('desembolsoReferido','UserController@desembolsoReferido');
    Route::post('estatusNovacion','UserController@estatusNovacion');
});
    /** File routes */
    Route::resource('countries','CountryController')->only(['index','show']);
    Route::post('actividad','ActividadController@getAll');
Route::group([
    'middleware'        =>  'auth:api'
], function(){
    Route::resource('users','UserController');
});

Route::group([
    'prefix'        => 'cannonAlojamiento',
], function () {
    Route::get('all','CannonMensualAlojamientoController@all');
    Route::post('get','CannonMensualAlojamientoController@get');
    Route::post('getEstratoAlojamiento','CannonMensualAlojamientoController@getEstratoAlojamiento');
    Route::post('create','CannonMensualAlojamientoController@store');
    Route::post('update','CannonMensualAlojamientoController@update');
    Route::post('delete','CannonMensualAlojamientoController@delete');
});

Route::group([
    'prefix'        => 'ingresoPrincipal',
], function () {
    Route::get('all','IngresoActividadPrincipalPorcentajeController@all');
    Route::post('get','IngresoActividadPrincipalPorcentajeController@get');
    Route::post('create','IngresoActividadPrincipalPorcentajeController@store');
    Route::post('update','IngresoActividadPrincipalPorcentajeController@update');
    Route::post('delete','IngresoActividadPrincipalPorcentajeController@delete');
});

Route::group([
    'prefix'        => 'dataCredito',
], function () {
    Route::get('all','DataCreditoController@all');
    Route::get('allAlertas','DataCreditoController@allAlertas');
    Route::get('allEndeudamientos','DataCreditoController@allEndeudamientos');
    Route::get('allPorSector','DataCreditoController@allPorSector');
    Route::post('get','DataCreditoController@get');
    Route::post('create','DataCreditoController@store');
    Route::post('createAlerta','DataCreditoController@storeAlerta');
    Route::post('createEndeudamiento','DataCreditoController@storeEndeudamiento');
    Route::post('createPorSector','DataCreditoController@storePorSector');
    Route::post('update','DataCreditoController@update');
    Route::post('deleteSintetis','DataCreditoController@deleteSintesis');
    Route::post('deleteAlerta','DataCreditoController@deleteAlerta');
    Route::post('deleteEndeudamiento','DataCreditoController@deleteEndeudamiento');
    Route::post('deletePorSector','DataCreditoController@deletePorSector');

    Route::get('soapconnection', 'DataCreditoController@soapconnection');
});

Route::group([
    'prefix'        => 'extratosBancarios',
], function () {
    Route::post('get','ExtractosBancariosController@get');
    Route::post('getPagos','ExtractosBancariosController@getPagos');
    Route::post('getCreditos','ExtractosBancariosController@getCreditos');
    Route::post('create','ExtractosBancariosController@storeEB');
    Route::post('createCreditos','ExtractosBancariosController@storeEBC');
    Route::post('createPagos','ExtractosBancariosController@storeEBP');
    Route::post('deletePagos','ExtractosBancariosController@deletePagos');
    Route::post('deleteCreditos','ExtractosBancariosController@deleteCreditos');
    Route::post('update','ExtractosBancariosController@updateEB');
});
