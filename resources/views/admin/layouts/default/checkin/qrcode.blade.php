@extends('admin.layouts.default.app')
@section('content')
	{{-- <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase.js"></script> --}}
	{{-- <script src="{{ asset('js/firebase.js') }}"></script> --}}
	<style>
        @media only screen and (min-width: 1920px){
            .qr-box {
                width: 55%;
            }
        }
        @media only screen and (min-width: 1370px) and (max-width: 1919px) {
            .qr-box {
                width: 75%;
            }
        }


		.qr-box {
			margin: 3% auto;
		}

		/*.qr-code {*/
		/*	margin: 2% auto;*/
		/*}*/

		#clock {
			font-family: 'Orbitron', sans-serif;
			color: #000000;
			font-size: 35px;
			text-align: right;
			padding-top: 2%;
			padding-bottom: 1%;
		}

        .title-qr {
            margin-top: 1.5%;
        }

        .text-title {
            /*font-family: 'Dancing Script', cursive;*/
            font-size: 35px;
            color: red;
        }

        .body-qr {
            background-color: #ffffff;
            margin: 2% 5%;
            padding: 1%;
        }

        .qr-code {
			margin-top: 2.5%;
            float: right;
			margin-bottom: 2.5%;
        }

        .text-about {
            padding-left: 5%;
            padding-top: 5%;
            padding-bottom: 5%;
            font-size: 18px;
        }

        .button {
            margin-left: 5%;
        }

		h3 {
			font-weight: 600;
		}

	</style>
	{{-- <meta http-equiv="refresh" content="28800"> --}}
	<div class="qr-box">
		<div class="logo row">
            <div class="col-sm-1"></div>
			<div class="col-sm-4">
                <a href=""><img src="{{ asset("imgs/logo-akb-edit.png") }}" width="200px" height="91.27px" alt="Công ty TNHH Liên doanh phần mềm AKB Software" id="logo-akb" ></a>
            </div>
			<div id="clock" class="col-sm-6"></div>
            <div class="col-sm-1"></div>
		</div>
        <div class="title-qr">
            <p class="text-center text-title">@lang('admin.qr-code.msg')</p>
        </div>
		<div class="body-qr row">
			<div class="col-sm-8">
                <h3 class="text-center">@lang('admin.qr-code.title')</h3>
                <div class="text-about">
                    <p>@lang('admin.qr-code.sp1')</p>
                    <p>@lang('admin.qr-code.sp2')</p>
                    <p>@lang('admin.qr-code.sp3')</p>
                </div>
                <div class="text-left button">
                    {{-- <button class="btn btn-primary btnCheckin">@lang('admin.qr-code.button-acc')</button> --}}
					<p></p>
					<p></p>
					@if(\Illuminate\Support\Facades\Auth::check())
                    <a class="btn btn-success" href="{{ route('admin.TimekeepingNew') }}">@lang('admin.qr-code.button-back')</a>
					@endif
				</div>
            </div>
			<div class="text-center qr-code col-sm-4">
				<img id="qr-code" src="" alt="">
			</div>

            <div>
                <p id="token" style="display: none"></p>
            </div>

		</div>
		<div id="popupModal">
		</div>
	</div>
	<script>

		var src_audio = "{{ asset('audio/translate_tts.mp3') }}";
		var homeUrl = "{{ route('admin.home') }}";
		var timekeepingUrl = "{{ route('admin.TimekeepingNew') }}";
		var qrCodeUrl = "{{ route('api.qr-code') }}";
        var getDateTime = "{{ route('api.getDateTime') }}";
		var scheduleQRCode;

        $(document).ready(function () {
        	// firebaseCloundMessaging();
        	startScheduleQRCode(100);
        });
        
        function startScheduleQRCode(timeLoop) {
        	scheduleQRCode = setInterval(loadQrCode, timeLoop);
        }
        
        function stopScheduleQRCode() {
		  	clearInterval(scheduleQRCode);
		}

		function loadQrCode() {
			stopScheduleQRCode();
            var paramdate = new Date().getTime();
            $.ajax({
                type: "POST",
                url: qrCodeUrl+'?time='+paramdate,
                data: {'device_token': ''},
                success: function(result) {
                    console.log(moment().format('DD/MM/YYYY - HH:mm:ss'));
                    console.log(result.data.test)
                    console.log(result.data.date) 
                    // console.log(result.data.query)
                    var string_code = result.data.string_code;
                    $("#qr-code").attr('src', "data:image/png;base64, " + string_code);
                    document.getElementById("clock").innerText = result.data.date;
                    if(result.data.query==0){
                    	startScheduleQRCode({{ \App\Http\Controllers\ApiBaseController::TIME_LOAD_QR }} * 1000);
                    }else{
                        var dateTime = new Date().getHours();
                        // var dateTime = 5;
                        currentTime();
                        //reload trang vao 6h sang
                        setInterval(reload,(24-dateTime+6)*60*60*1000);
                        // setInterval(reload,60*1000);
                    }
                },
                error: function(error) {
                    console.log(error)
                    $("#qr-code").attr('src', "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANEAAADMCAYAAADtckaqAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAADhKSURBVHhe7V0HeBTVFqaHUAKIKEixUEJvggUbdkVFLChSnw8R9SGK+hAVEAEpShFEQFB4FBFFSjYVQkhCCgmQANKkI8WEUAUkAYLnnXNnJpndPTOzyWTZDd4/3//NZuaeW2buP/fOrSVAQkLCFqSIJCRsQopIQsImpIgkJGzCVESXL1++Jvn333+rKSw4yJbzU+OVK1dUl0UPX4ZtBQqbi1NxZ25urppCY5iKqHbt2lCuXDmoUKHCNcMSJUpATEyMmsKCIz09Xfhh5PewYcNUl0WP9evXm4Y9YsQI1eXVx6hRowzjVlxJ6XnyySfVFBrDVERVqlQRHl1rXLVqlZrCgmPjxo2snxoHDx6suix6pKSksGFq/Pjjj1WXVx/08uDiVNzZsWNHNYXGMBVRtWrVWI+LO70pog8//FB1WfSwEtEnn3yiurz6GD58OBun4s6HHnpITaExpIgKCCkiHlJEBpAicocUEQ8pIgNIEblDioiHFJEBirOISpYsyZ4n2hHR9u3bWT81monITtM6YcuWLWyYGj/77DPV5dWHmYjMnoW/06sioibABx98EO6//36/IiX6tttuY+Os0UpEiYmJEB8f78bU1FSYPXs266dGq5KIhLBmzRrWfzNS2DNnzmTD1Ni7d2/YtGkTa2/FhIQEW/1MViVR69atRUsX98x8yXvuucdU5F4VUcuWLVVX/odvvvmGjbNGMxFRRuJs9DS76VYiat++PWvnKY3CLoq3fXZ2thrLgsNKRFu3blVd+h/KlCnDxpnoVRE1a9ZMdeV/mDBhAhtnjVYiqly5MmvnCa1ERKU3Z+drUqe6N0WUnJysuvQvnD9/XoqIgxRRwSlFxMdbisiAUkTulCLi4y1FZEApIndKEfHxliIyoBSRO6WI+Hj7TER2+0M8gVkYdkXE2ehp1hJmNZL6scceY+2KioVtpSMRWcHsnvu7iIzi7tclEfU99O/fHwYNGlSkHDBgAHz55ZdqKDzsiIhu9tdffy38mDRpkhPpPMWB81MjNWHTaGrXeBM/+ugjqFu3LmtHLFWqFIwbNw4mT57sFrYVqQ+Jhu1z/nrC0qVLw8CBA+G9995zi/d//vMfmDJlinqHeNgR0aVLl+C///0vvPvuu25h2+Wbb74Jhw4dUkNyh1+LiDIDZ1cUvPXWW9VQeNgRkRV2797N+kn0tBQwckcP0w4mTpzI+lsUbN68uRoKDzsiunDhAmtTVFy3bp0akjv8WkRfffUVa1cUbNWqlRoKD2+KyGrsnB3Sw/zrr7/UkAqO0aNHs/4WBTt06KCGwsOuiKg6ydkVBWkyoxGkiAwgRVT0lCIyhhRRASFFxEOKyABSRO6QIuIhRWQAKSJ3SBHxkCIygBSRO8zmE9kdSW1XROPHj2f9LQpKERlDisgF1NlKc19oTlLDhg2d2KhRI6hXrx7rp8bXX38dsrKyRFO4KzMzM+HOO+9k7YhWItq8eTPUrFnTLV7Exo0bQ/Xq1Vl/i4JSRMaQInIBicjOA7Waov3www+zdkQrEVF/B2enp93S0IhSRMaQInKBL8fOWYnIao0Fb1KKyBhSRC6QIuIpRWQMKSIXSBHxlCIyhhSRC6SIeEoRGUOKyAUkooCAANbOE1qJiFaY4ew00k4ERvBmH5UVpYiMIUXkAhJRcHAw1KpVC+rUqVMgBgUFiekOf/75Jxw+fNiNZ86cgWeffVY0Rbva0g4cN998M+zZsweOHj3qZnv8+HGIiooSz8TVtihI4Zu17EkRGUOKqIhhNQVk7dq1qkseNK+HsyPefvvtqivvoHz58my4RCkiY0gRFTGsREQLNxqBqnLavjgcqaPWW7h48aIUEUMpIgNKEblDioi3lSIyoBSRO6SIeFspIgNKEblDioi3lSIyoBSRO6SIeFufisibw/JphLUZvC0is6Wj7IqIs9HYtGlT1aU7zOLkKXwpIs6mqFhsFypZvXo1dO/eHfr161ek7NOnj+U+PHZERJlx7ty58O2338KsWbOcOH36dFi2bJnqkocdEVEfFS1P1bdvXzbdY8aMUV3yoJ3NaVkv13hbkbaLobSVLVuWjTPRmyKiJbNoaavXXnvNLd122bNnTzhw4IAakjv8VkRF8Va0glkYdkREGZmz0digQQPVJQ87IvIEZumm9e64MIuC3hTR1YDRffPrksiXsCsis7FzVh2e3haRGYrr9HBfQorIAFJERU8pImNIEblAioinFJExpIhcIEXEU4rIGFJELpAi4ilFZIxCi+ha3vjYn0Vk1jpnJiK7C5jYFdG2bdtUl/4Hn4moUqVK8PTTT4vtPPyJnTt3FstHcXHW6K8iys3NhS5dusDjjz/uli6aEUtbj5jBqiSiLWko/IiICCfSPCWHw2E6asCuiO6++26/yy+dOnUS+0WZvWC8KiJ/p9mN8VcRWY1YaNGiheqSh5WIaGasGbw1YsFby3hdDf6jRWRGfxaRnbFzViIymxDo7bFzxZVSRAaUInKHFBFPKSIDShG5Q4qIpxSRAaWI3CFFxNO2iKpWrcp6XNxpJaKKFSuydsQ2bdqoLnmMHTuWtdPoTRGNGjWKtdOYkJCgunQHhW3WOnfXXXepLnlcqyLq2LGjmkJjmIqoRo0awiNqR79WSOmJjo5WU+gOEhG9PKhFibO94447VJc8vvjiC8N7RudjY2NVl+6wKyKaKkHujMKOi4sT7igcPSnNtGgkdVvQDuac7X333SdsjTBixAjDsIsrKT2PPvqomkJjmIqIJkvRzb3WSP0xZjBLd3Z2tuqKB82N4ew0UoY1gl0RmYVNoKoJ+UNh6EnVuCpVqsCpU6dE+jh7u+kurszJyVFTaAxTEUlcXdgVkRXuuece1l8irXdHQpAoOKSI/AjeFhFVyTh/ifQ9ZFXaSPCQIvIjSBEVT0gR+RGkiIonpIj8CFJExRPFVkRGUwLMpgr4C4ziaCUiGgltBbP0t2vXjvVXo10ReeOZeGrrjbA9hamIaCIV9S3Ex8f7DamfJS0tTY0hD9qKJCYmhrX3JSlOp0+fVmPpDisR0bpzNBLbyG+zpaEIAwYMgPbt24s9kvS89957RfM3jVowAm0XQx3FRmEfPHhQdclj06ZN4tlx9tQJbJbZaUsao+dJ53///XfVJQ9aSswo7MTERNtCMxURdbxxD9PXtFq80Wr4iy9pNlrCTESeTCd4//33VZ/cYTej0JAhLkyNtC+TGZo3b87aaTQTMAmFs9E4bNgw1SUPq/lldN/twFRE/jp2zu4ywr6knWE/VrTa/t8OkpKS2DA1Wi2oSVVRzo4YGBhoKiIqMTg7jZ9//rnqkgeVvpwdkYZ4SRExkCIqekgRGUOK6CpTisidUkQ+oBQRTykiHlJEDKWIeEoR8fBLEXm68IS3FqioX7++GkMekyZNYu28TU/Su3LlSjWW7qCHydl4Sk9EVNhWus2bN7Nhavzwww9VlzzsiMiqdW7o0KGqSx5+WxLVrFkTpk2bJjKsK2fMmCGWfuLsPCW9XWijMFe/J0+eLGaP0vJRgwYNcuM777wjJohxtlYkvydOnAgBAQFsnDwh9bfMnDmT9Z/m+xw6dEi9u+6gaRJTp04VJSlnb0ZKL83pofRz94W2Ltm3b58akjtoesjgwYPZ+0rnhgwZIu4PFzaly2wPIIIdER05ckRMduTCpnySmpqquuThtyKyWryR9vfh7DylGfbs2cPaaHzvvfdUl4VDUFAQ668n/PTTT1VfjOGtXnSr/jGzqiRlYs5GY+vWrVWXxjBLV2FF5Om9MnPntyKyWkbY7naTZj37VlULs05HK1BpYLbGghWtqjXehC/XWLCCnZLILqSIGEgR8ZAi4iFFxECKiIcUEQ8pIgZSRDykiHhIETGQIuIhRcTDb0Vktbj6lClTWDtPaUdE1BxrBaPWHG+KyFutchqsRJSSkqK65MHZaKS5SFYwSx+t18f5q9Eu7LTOWcHquRVaRDQTMjg4GBo2bOjGWrVqwQcffAAZGRmwe/fuQpEysxGsRETxpuHvXNxuvPFGMYfECN4uiWhL+Nq1a7Nxs0Pqt6MpAUb3/OjRo6Lvrm7dum621HndpEkTsYfQ3r173WypbyskJARuuukmN1sirU9IW/+bgfzYtWuXm98UHoVL4VM8OP/NSGFPnz5dDYWHmYhoug/tCM/5Tc+pV69eqi/GKLSIrDhw4EDVl6KHmYg8GTVA+/EYwdsiMlu2yi6tRiyYVamIZm9c6tDkbDRazekxA91zzk9PaTXkyExEVqQJi1bwmoio59xbsCqJrGhnLW4rWomINuvi7IqCViKys8aC3bFzZqDFMil8zl9PaGfsnBV9uqB9UYooN/cynNm1G06mpXvGdOV4Co+ntHOb0uHEpk151zSSm7z/8fopYloa/k/UXeNIti7+OVPvh97PNDVc7Zx6fqNyFNfS0+AEHTdtVq7l2apM34TpoWuYpo26827EsLTfLvHNO0+/1fP6o/ZbUPutXdfOE0X4GA9xf5HqeSc32jl0czod463G2SkMQfUeqMw7h/fm9I7f4MKfZ9Rckf8NJ0XkATKiYyC6aRtwlK0EjjKVwVG6Ih6R6jFEf9T9Vs5XgtDyVSG0ArJcZQhV7UJ0R0FyGxCE7q6D0IAqTmFo1zWbPNL/Ojd5/+vP6Y7cOToq8VSO2v+hZYMgDOMSFlgt/5p6XfiF9yE0ULkeivcl7xq51R31NuJ/9bf4Xzvn4ibPLR6FW70b3W9xnY54jx14j8MqVIMwg3tM7oih5YIgnOKN91j44eJGf1R+0zNHlq0MkTfWg/S33oZcdaVWLa9JEXmAvZOmQFT1G2E5+htx/U0Q06gZrGnUHNY0xGNDOnJsgW6QzdpAXLO2EBvcMv98nhuyVyn8Q6K7WLSJRdHGOLklcrbcNe26/ugZYxs0h7gmrSGuOca5cSvWTR4pfU3RbbPWavpc46dz60pKLzIW/VCI6VZ/u6dFdS9+a/6SG/U33S+Kr4iDdXpFWGSD8V6D6c23cT0ig5vDqrq3iZfgMnz+qxs1gezjx0W+0PKaX4uIdgngPPaEdgeB6rFv6jcQVeMmWIr+7hj+GWTErIHf5/8Amb8sh8ylK+CYjpm/EEPwtwOJx2X0G0nn6Tq5U38f/WkpHPnxZ8GjP/0CGXn+hUDWMgf+RqJf5CfZKGHg/3lU3aj/ZyxZLvw8/OOSPH9deXTxL6o/alzQTom3Gg7+rw+b/s/ANGQixRHd/PHzMiXui36Gw0QR3hLhtwgHz+nDPEzHxUtE/IT9kmXwO7rZN38R7P3fD7Bn7kLYO2ch7Jv3A+xf8CMcQrd/oFtKM4VFfuv9y+MiDG/x0vx0oHv6rfzPUb1P+FtJkwN/K2lSnoUajhr/wwt/gozwlXBwzjxIeuhx8fzXNG4N2SecRTRy5EjxvxFo8Kw+bxaEtkVEK7RQ02mdOnWcSM2k1FTMBaqxb9++cP78ebF8lStpiaOCrHG27+vpEIUl0C/o747PRsEZrB8fjI2FlMS1EBWyAlaFKoxGrg51wOqwMPw/FOlAhuRdd2YIHNi9Cy7m/AVw5TJkHT0EqfFrYGXIUnEtmmwdCteEhwubaEcIxKC/Mfh/XFQUJEavhpT4OEhPSYbtm9Ph4L49cO5PtX8L/bxyOQdydfz778tw/I8jsMahhB+NfhMpjJiIcAwzVIRBYa0Oc0BsRATEYzhJq1dD6tp42JSyDnb+ugUOH9yPH+Pn4G/6u5KLfl9UqYWV//sK/laicwkyDx2A5NjVsGj2tzBhyGAY2r07fNCpE7zzyKMw6NFHYUjnzjD61Vdh1mcjIPSH+bBxXRIcy8xA61yAvymc/LQIvzGNpzIzYX18LD4Hum+u9ziflJ414WGYnkhIXB2t3Ld1ybADv1X3798Df54+KeKpD+cK3a/YeNg7eSqse/aFfBGpJRHlRXrR0/QRMzz11FNQvXp1t3xMpGZsLv9qtC0iM1AbPxeopwzDjO4p9n09LU9EO4d+Cjlbt8KqadPgX63aQHc816NEGeiJ7FUyAP5drgK8WiYQepcsC73U83TdmaXRbRkIn/gV5KDQCZSB32zTDp5H//Q2vUqUhX+XrQh9ylaAHiXLKX6i3z1LlYdeeK5PhSB4tVoN6HfLrfDuXXfDmF7/gsXTZ0ImZi6uyTghMhLuKVEKemMcepNf6H8frPv/u1wl6F2qnFPYSjgB0Kschl+xCvy7ek3of8tt8ME998BYfEmFLFyA4VAmN8fOX3+Fr0eOht4d7oVOlapAV0zjW8gPkZ+VKAnjMS5fIMdjvEbhuY+Qb+P5gRjeqE5Pwy/fzoDDR4+ovjkjZe1aePP2dvAs2tC90eLuSpEuTE8PTGPvsoHwr0BMT9Ub4PW6t8A77drD5z17wk8UzpEjooVUwwkU0Z4Jk2HdM88rIsKqriYiPaw6RI1AoxW0vYg4elVE1EHGBeopI/At6ym06hyJ6DcUUfbWXyHqm2kwtHU7WIEPx1GivPg4DS1XBULxuKJEAITgOWMGwAoU3N6J0yD3vLJ3TwSKaEqbO2ARhhHq4n4FUnzcBgShnfL/cuRS9OfnEuXgRxTCPIzHLMx4X6H9WHTzxf0dYXtKKlzOde40TomMgl6lykAY2q0oGSgaMUKpsaRkBbcwl6P/y5C/oFsK5wcM53+Y2WdiGBOQYwIrwzddXoTNiUmq7+5Y+v1ceKFVW+hSugx8gvGbg36QnxGY/kh8KURgOOH4v0L8jXGPoOvIZeh2FoYzrnwgzHiyE6QluHdSJyWshYkoAnIXiunRp8GVlB66Z0vV9CwS9620uG9TKBx8+X11/8OQHheXNwzoeOxa2PPlJEjVi0itzhUFqLb0DxERlkSMiIahiBz4EEJLV4CwgMriIZIAhKhMGYCZNgD2T/xaJ6JwmNrmLhQElpKsTXnhv4MyP76xifSbwtNIdpQRSSA/oD+L2raDjLR04f/foIgpOTwC+uAbOYIya3kUUKmKTmFoDMEMtgIz1wpMn4gvnnMNh9zMo/g+8xxk4PPQg97MYQsWwCM1asL7FJeSpYUwyE7xn4gvE/TDUQoFLATg/PIhd8q9KIsZviQsCG4Bhzdvxipk/ls/GUuiCe3uECLS/OZJYZVCfyk9/H0LRTeL8fqC+o0hI125b5qIUjQR6apzRYF/jIj2Ykm0Uledcy6JSuc9cH0GUBiI18uIVh2O+8ZPgtxzSnWORDQFRcSVRHpGVq8Na1rcDqtubSxKkRDMEJw78oPC2DVxElw6dxYu4bfLJRTS2hAH9MEqTRhlWBQSZ0sZO+qGuhDfrgOsuqWhcOcwcEuZMrxyddgxbIRIh4bjmcfgyVsbwnCMH5VklFGd7VCYKJ7VDZvAvomTYffn42HlDXXEeWd3CinjUxdDQqdn8pqYCSSiL1FE32Ja3cPQbLFkq3KDuG8xDZpAeGAQnkfxsm6V+7Zz5CjIOXUKjicko4gmO4tIlkQKPRUR1Y/3TpuOJVFtWIJ22z79DM5v3QKRLiLiuBzfnrFY/P/6+gDY+dEw2DH44zzuHPwRnIhLhCtqtcETES3DN+n657rCyZQU+HPHTtjYpy+eK4Nx4IW0Av3a8HIvOLV1O1wUzQBY/cFvwd74PSZExNgQKbOk9e4LOSdOwvHEREjp3hPPl3Fzp5CqRyVg/Us9sPhRSrtcrOcnr4qGroEVsUQMwPS4V7NIqOFVboTteF/IiuL2v87Pwc8VquJ1FAzauWZyOheJL7Ns3XeYJyIiwSQ+8BCcSl0P53bvgbS+/bFqXFnEgXO/HJ9p0uNPwdnfdsHJdamwF7+JUjvLksiNnoro4qWLsOvb2RDdrA0sD6wKWz8fA+dIRNOnwycWIhKZsXsfuLCfX2xd/7XiiYiWo4jSu/WCv/YqC37Ez5kLE6+7ATOPc2ag7xnhHquMSSi6rI1pKCIFyeHhojpnJiIqcTf3HyDcX8S3ftqKEJiMLwTOLZHSmfxEZ8g5qbRw0ffEkm9nwdCAivjiIUG421AJHYElz65vZmApmQOXcnNh/MCBsOi2YIi46VYIrVQd3VEc8+NJLwtHxapwOj0N9apWTz0QEYkl6ZEn4Ly6UEriosXwXYOmeI2qd5z7MhDdtDWc2pgOp1I2OJdE19I3EY3K5QIlejII1OFwqD6Zg96Qe77+Bqs3dcRN3D78Uzi/fTtEzpgFw9q2x5vOPwgiud/4Sm98eOa7JRA8FVHayz3g/J49wiZkwXz4qGZt/NbQlxKY6VA8K/B7J+qWYBTRS5CZvC6vJEoM81BEr/9HhHERS5Wk0FAYhOc4t0Qhokefgpw/lBIiJzsbvh87HiZg9Ys+5J1FRPHDY/kgWNWyHeybPQdyRcwAhvXvD/MrXweOCtUgvPatEFb9JmVUgmpLIgoNrAKZK1fB3+ocHI9F9PDjcF59+YT/sgS+aNoS41bSQOBlYSWK+SSWXFYiKmyrnB5eFdG8efPE8k+zZs1y4ty5c/O2XDci9TG9/fbb0K9fPzf26NEDtm7dqoZijsv4htw/dx6sxptHN5G+ic5v2QKrZ30H4x95HELKBeFNd696EMl92it94C+DkkgPj0XUrSeWRHuFzdI5c+C9atfjR7FWEqF4SleAiOtRWPWbQvgtjWD9a2/CiV+34feQgnWRUQUqiXKwJEpYvgIG4jnOLZG+IVKeehYunf1TsUERzcCXzdflKuG1/HtDR0eZChB6XU0IxW+uyLoNYPekqXAZq4FXMDN+OmAAzEMRUdWRhtmE4YsrrGY9cOA9VkokFBG+HP5YFgJ/XyqgiHQl0YpFi2B0g8b4fKhxhnNfBmLb3IEl3haszq13E9FfWVlOmzSvX79eTMVwzadW/O6774Sd2e4ntkXEeaqnWYnjyQxPT98iR5atgNQuL4Gj0g2w89ORcAFFFPXNNzDywUcg8qb6mLn5bxLKXKnPvwQZ6zfCydNn4ETW8TyexHq1vj/CMxGVhA1dX4GTW7fB0YwMGPnGm/AanosQ4aOAsJ7vQAGF3VhXiMhR+xbY8vFwOHvwd/VdrzRxW4mIMsumvm9gKXQJtm39FUb1fR0GG1bnqLWwNH6fvZYXRs6FCzD946EwE0sivYjILZUuYbVvQ5tyEIki2YcvKA0zxo+DH2vdrPqJpWvZihBBaalVTz2H31cBVeHokqUoIiUTeyqi5Acfg1PbdsBxrHKOwJfr2wGVMF5cVTNQvKzW47fkOXz5nUxKEf1EKc88J+7L6qZt4Owffzh11tPaeFweLAraFpG/jJ07vHQ5pOC3haNKLdg6cjSc+20nxC74AUuix5TBiwYZUjwk/Hge16gpdG3eEl5p2iKPr3W4F86cOKGG4JmIKJyo6rXhk2at4JmGwdC1YhUYhxk4klrPsAoUcWtDiLy1KawIrIbVuWqwslErOIqiuaJ+8BM8ERE1KS+tURtGY3X18foN4IWACjDbqXVOaZrWWh6jMX00DEqDIqJh8K2LiEg4YddhKVnrNsyopSHqxnpw4H8LVCuA7yZ8CT9iNS78hnoQ3bglRAe3wN91UUxaHxbaY6l05Mef4EoBRETxjQ66HkZgFe7F4MbwXKUg+BzjHu7U4EFpokYa/MYtGQiHF/0IuVjaUeMPNXGv6/w8pqUsJDz0JJzN+ANydNPJP/74YzYPFgWvCRFRj/LBeQshulVbiKh5C+ybPgOyD+yHqNnfw9BWt+NNN/4mIlLG+QndzMc3+QIdf65cHS5kHlND8VREROokLCn8WyIyciB+bF8PUTc3hNDqtSD6zgdg7R33QsrLPeGIIwIunjunhqDAExERl6Pf1GelhKNzS2LCKmNU9ZoQ17gFrH+hGxx1hInqmIZsAxFRNS3supsgvBaV3mUhEkul3RO/VqpzWCp//t478Mvtd0NChwchFo9ULVXslPDFNxGK6BAK9srFgoiIGJB336ijVWt8ESwVKJrAafBrysOd4BC+IC+p942G/VB1bn2PPhBS8TqIaXsX/IklkRRRAbHnm5mw9t6HYE2rO2DfzG/hPFZxIqZNt2zi1pMykp5RmOGzjxVcRMpoCGpIwGOZSrAcP9Cp1dBRtSZENWwBKx/vDOH4PbJ544a8B62vtHoqorwqFbrLFwHGvWQAhGGVKrz9PRAxYiTswG/Liy7VYmMRoQhoesWNt4Kjcg2IvL4O7J00TcQvF4U0c/xYiHi0E6xu2Fy4cx35YVdE1NRNcXA9H4bfXxFNW0HkoA9gQ/I6p++dY2viYCcN+8Fq9AoMO/6hJ1BEmVjVlSIqEA7MmQ8pT3SBcHzoO0eNgQu/0jeRdT+RQqXqQ29epQpED7IsRFStUWARUaYKr3wDrKzXAMKxuhWGVZ2IurdBCPq1HKtz1CK3JCAIJqP/o7Bqt3jkSDiRmamGoMATEVFmjcBq6OpbG4s3tD7jkSAoHguxdP2iVACMCm4KoV99BWf+VBoVCMYiUl8CGEcHlkIrm98Ou6fOFCK6nHsZZnw+GhY3aCJKBiXT59sptqqIsKQoqIhoblR0PazuVr0RHIz/i/GeTcWXxqib68O8/34IWSgUqgRnxK2F3yZPgaQuL8Iv+KzX4DM6m5WFcc5/cUgRWeAyvpX2zZsPq/DhUmfrzk+GsyMWeJaBKMzkiY91gpTnu8G6517OY1qvf0POmfxZkp6IiDpbUzq/ABnxCXD8162QNmgwRNRvhmwC4fh9QePxlI9lGlNXUowJ2zBpMmTrMrinDQsbsfryV0YGHFubCOte6Cq+YfRuSBgU1k/odn71G2DdmPFqCOYiUqi8WKJubgx7Z82Bi7kX8e1/GYa/8SbMrYSlKt43dxsiiggFeGjhogJ9E1FYCfc8CFkJSXBu337Y0OtVjBe9zPLvgZKeAHzGpWEO3rfkz0bCKay2nVi/AfbOnA2paLOiXGVY06QNZB/PEmFr+EeKqCBt+5f/zoU9U2gUd03R9Ltz2KeQvQ1FNM1aRJQZN3TrBWf3KE3S1EHoRF08PBFRXmer2lS76psZMLnOLbAKP8SVsWf5bpVMUQLWPf8inNGNa8sXEd+iSBRN3G+8LdxTK1TygkXwDZ7j3FI4NFkxFd/UNPVC2FzIthARZewy4ptoz6SpcAnvMd2JT/r1gzkmIiIx0FCnjJ9+KXDrXNIjT+Y1ca/5/n/wbZ36eJ/dnx3dewfe54R7O8Lx9HTIXJsAu7/6GlJf6iGeZ2yTVpCjaxAieCKiwm7zQxP6rOCzkohWh3nggQfgySefdOITTzwhjtSTrEEbO0c3kRs7F4rVA5oi7frgxRu9W284v3e/6pMxPBWR6Gzdq3S2hi1cAMNuqoeZx3nEgkYaQJr08BNwemOacE9QRFQGwkuWw2+BSqydvrM1B0uIpBUhYtoC55Yo+ome7AyXTp0SNqIk+mQYzMI3N42c5myEiK6vDXsn5y91NfT1/sYiogYNaoVEEdGEvQKLSNfZGvbTz/BlcHNxP93DofGO5SCmRVuxrsLxlPWwiwagPq00ces7Wymf0MIvNCmPdrygUTCujIuLg0aNGrF5lEgLVoaGhkJkZKSbLZ232raF4DMRdezYkbXTqF+8cR+NWNAGoA4bgSXRVqeSyEGlAH6cigGhuhKh6EVUGku2nnBO7WwNmT8fPrqxNoRjpuPdl4CE+2jM2AbhnpBfEuHDwziHB2CmdSnF9CK6qIrIrLOV0pn8GI1YUL6/qLN19ujP4bsg/FZzahrPJyeiT/r1N67OYRwdZStCWOVqcMwRbmvEQuiSJTCuSQuMd76I6Eij8UPLK53n0Vh9P4VVuVMoIqMRC1pesZoefueddzrlLT19unijFa1ERG8Rzk6jk4hM5xPpqgTUG48f945S1HekZK607q/ChQPGG2tpMBMRZXLqtafMkNYtf9iPQxVRmIGIqPk98Z6OmBHy32au30QOGuFAC6mI/i7FjhPRO3hOu+5KIaJHn4JsddjPxZwc+HHK1zC9PFXnjMfOuYrow3/9G+ZWrILp56tzNJI8POh6OLk2Gf7OzRU2hRFR+M8/w/gmzTGdiojIX6pJhOF9CClZQbhffWuwENDJFPwmogGoNkRktsaCT5cRtmJRikhfnfuNKYm0hyUefKkKYvIcDdunkmDdE51hV3gEbN+xA7Zv+dWJO7dug4vZyneEkYhouoDiH406dh47Zy2i0pYiEsSME45hUFMvCdauiKiJOGrRjzCxXCBeMxERfRN9+ZWwIbzz0kvwfSCJiJ+mQA0LYZWuw5IY06++nQtVnVNFRGPnaI4WrRCkjOqm+VpUnUMR3aKIiB07p47i1vKKFJEBjURkNJ9Io/bwHbR0E1Xx8KFPuPEm+FedetDXhf9p3BSO7VOqehFhLiKiahAt2UQlWxnl2yXvm6ioRSTcKtW7MBQTfeNs7veWcO+xiB5/GrIzlOocdZzuxpfEuErVTEZxoyACq4mpGqcxUx7efwBebNkKZtH3JfsdhefwntBKS1pHKCEpHkV0e/6kPHc7IxFRda6MmEyp3N/8Kq2liGRJpKCg1bm8SXkmJZHGvExDa5ehmH7GjDEf3bnyxwrXwZndu0UYmohoRmoolgyOslj6YBWOSjbNX2+KSCOVfDQPaet/lPtnJSJ6c9O3WvLTXVBEfwgbwiX8LlrcvTcsC6AMyjd80H1aXrEafN7+LujWogV0K1seFpC4XNwRqeQKq3wd7B4/0ekbYV1iIky6uwPMxvgZpclVRBH4TTShZRuMNwoHq95OoxdU95qI9NU5ev4kohwpIgXUCsfZafxT17ey92u9iDxv4hZiwuoCLdoYjoKgyWl0jkoaYmTQDXBOFYQQUVsqiUqL0oBKMsqgev+uhoiI9Mbd/IY2n8hIRJgWqmpiuqj/Kvmxp/OmQmg4eywLEjt1QTdUtaXhUVyzOg1jKgHfI3/GzEtrSDhfp47qkvgtdANsemsQXBadrPkiSkqIh0l33Y0iKg2haoniWvK5iihy6VL4snkrTGcp8Uz0bjX3V7M6px+IXBgUWkTU7k7zMDjSddpxmkAqdyWhU6dOwp2rbenSpcXxhK4vQGmdq+VUEkVMnQpDmlG9mp/6rZG+i4iU0ZRRC6XyzlM9/OwupSQKD3HAVyjKhXhec+NKCn/9C13hnFp6LZ8zFz64jiavubslUtzi7+gAp5Lzd9ZODouAV/A89SFxNkTqQE1/rZ9wTyJKXL5crM6jd0MCpSoZlULUCZ348COQfSS/JNJw4fQZ2DFyDMQ0b4ulMn0jUdpp7QYSvjIyW5nFSqLOH9hKbiic0ApBEH9vR9jz7WyR2VzbqRLiYmFc27Ywg9wKwSmDYpV7rpDCTLj/QXz5KK2ajh9/hNGNGol06t3p3a+qfTPetxQhIm83LFBfHKWNy6u0q7oVCi0i2gqD8Ndff7mRQNujk7sKFSq4kc5rk/JcbWlxczrSene0yDnBqTon5hNthg2/LIEf/vshJHV5Cda9+IolU4gv9YBUrOKkdO0uzq3v3Rey1WE5mzanQ/hHn0Bcl67Cras9MbnLC7Br8hS4oH57rI+Ph//17QcpzxvEocuLsHXocDi7Wym5CL9t2QJf4bUNL3TjbZCJnZ6F/bO+F+4v5V6B39I2wXcvKGGIdGBpmNKtl5KOF16BJMxgO0aPhWzdOtWuOLNrF+xF0W98cwDE3f8wrLyloWhpo74qGglPS/fS90lYtRtgZf0msPbRTrD5/cFw6JelcP4Iv1wWYcfOHbDiwyGw8pnnYAPGZz3FC6nFlY7Jz78MO0aNgRx1wO/6dcmw5N1BkPgsf6/JffpbA+Hs/v1wIjkV9hqs9qPlRTsiInJ5lEjX6LPDCoUWkd2d8qhzywzazgwErYmb3ri7Rn0OWTExcGDxz3A0MQky1sRCZoEZB5lxayEjLg5ObtwoFmE/hlWuP1AUGWvWMO7zScNwTmzYAKepN31dChyJRb8YdwrXwDH082RqqrpwexpkpaAN61bHmDWQhWlTFndHm3WpcBTjfAw/4jNi4yGTwkQ3evfH1ibACUoLuqcF+Z24MQ3O/LoVTm/fjuGnwuHQMDgwfwHsmTETfps8FXaOnwQ7MKPumjIVS5xv4cDCH+AIVjuPo39ntu+A01t+RX826qj4S2Ep9w3jpY9/LB2VtGfGKfE9lpAAJ+m+peF9w3twNJ7um8m9RrsTqRvgMJbCe6ZOg9TONHauBMTo1ljQ8pJdEZmR+jOt4DMRUY+wGapWrSrcEfZOmQaRqoi2f/gxvp2SITN6jVhOiZilHl2pP0+/9TwWm4gPOREf2FrIXI3iWB0Hx/G33k5z6+RHTLxwewxtsmLQRn9N/Z1/Lh6yKPPHkP8KhY0aTr4759/HiGvQNhptiBjm8fgkjG8CZsgEyKLfwi26UW2y0L0WrzySrf5/JMX/eBz5kQjH1yYh6ZgIWfibxrZl0f94LYvcYFyzhB0KYnWMOGaqx3z/KD3opxYPJMX9GL6kjqGfmeQX3uusGPw/L36ajUKy0adfSQ8+F4z/cXyBHJw7X4y9o5IotnFLyM5Sxs5pecmbIvLq2LmrKaJ9M2dDRI0aor5MH9PUnxIuWAnC8WNWHM2IbsKI+JsYXq4yRFSoDhEVq0N4JTyWrybchVGPvGZDdPE7z96F2nkKw/UaRy0ubuc16uNLjSIVrhPxDC2n/B+F8Y6kdRDwt+ZW+KEdVWrnubDy6GJjdT/ZeKONFl9xr/HZRFSsptzfQOUYFlCFde/qn/68cg3TKDqiyynVueCWcFFdqlnLS1JEBtBERB+yZw8cgA3/6gurmrSElQ2aQJTGhk2R+t8a1f/1bpErG+C5Rs1gVbOWENW4OZ5rKs6tbNoCoprg/5qNsNfI+Of0Wz26Ms8v3VG4bWzsxvV8Izxi3FZhXEW61ThF4vVVTfB8U0wHpsfJxsTPSDwSxTWN4v98N3lHfbq08y7/r9SO4j4q11YGN4NojBfFWXO7Mrg53vNWEBWs3G9xXk81Xdz5SOEeWb8xxLTrADvHfiHyB02F0PKSFJEB9CURCSnn9Gk4//shOLfvgJh7f27/ATivHhUeFP/nn9NfU0jXztLx4O/qOeX/cwfQLVG1P6+619xots7n9P9z1K4rYZx1sRHhurlRrinn1WsiXvprOmI6zuN1Jb75ftHRNR3a/wo1/zUbjfl+5NvSb2N3dHQK6wDeW6J4ToobEXeMK70MFZt8/5zTnX9O/7/gvv3w15E/IDcnfzKelpf+sSKiUbdm0ETEsVWrVqorHmPHjmXtNJo1alBTp9l2k7fffrvqkse4ceNYO41m6aYmVc7GU1ptN2k2ENMurbabtNov1oieTGH49NNP1VB40MpTnJ0n9GsRffHFF7BhwwaIj4934tq1a8XRbG8kKxEtXrxYuLn//vvdSFvBU7hG8KWIqE/i0UcfFZvtcnE3I22RT9NLuHtKpCH9tBP2XXfdxdpTmGZLR1nRWyIiVqlSRWRmLt6099D8+fPVUHi8/vrrcMcdd7D2tBG1mVD9VkSFnSCl0UxEnk74M3LnSxHZxejRo9kwNZq9PAg0t4az84TeFJEnGdnoeXqSH6hznwuX6NclkR1alUR2cC2LiEp5I9DSw/4qIioxvAWvLyMsReROKSKeUkQGkCJypxQRTykiA0gRuVOKiKcUkQGkiNwpRcRTisgAdkRklZmIhW2la9u2rRpK4eGr1rmNGzeqLgsHs9YmX4qIuizMcN9997F2ntBMRJ60vhGM3J07d44NUyN1HVjBayJKT08XPck0JcKVM2bMgMaNG7P+esIaNWqIXSdoN4CC8o033oBdu3apsXSHt0XUtWvXQsV9wIABonQ3gy9FRCXNRx99xMZ9yJAhUKtWLdbOE1qVROHh4dC/f3827DfffFPspWUEWo9i8uTJMHHiRLd8Ss9yyZIlqktjeEVEnrwdOnfuzPprRbt9TESzIUfeFJHduGtzuIzgKxF5mq7Cpt9KRCQWzk7junX5kyILA6v87LWSyApWayx4k6tWrVJj4Q5vl0R2SMN2zODLksibtBKR1QqotAmYNyFF5AIpIt7Wl5QiMoAUUcEpRcRDisgHlCJyhxRR4SFF5AIpIt7WlyzWIjK7qbfddpvqqnCg1fw5f68GfTWfyG7rnFUns5WIrDKTv4rIarEQKxGlpKSoLr0DUxHt27dP9Kns3r3biXTu4EHrbe3NcPToUfjtt9/c/PY2KUxakssI3i6JFixYAEeOHGHjZka652b9HQQrEdWpUweCg4OhYcOGbmzQoIGpyGkeVkZGBhs3O9y7dy9s3boVypYty4ZLDAwMNIw3La02atQow7jR89bvNO4NmIrIW/C0l9kX8LaIEhMTVZdFDysR2SFNXvMW6J7TGoNcuJ7Qanq4t+ETEfkzvC0iX46ds8MOHTqooRQ9aMFOKaJrCFJEPKWIjCFF5AIpIp5SRMaQInKBFBFPKSJjSBG5QIqIpxSRMUxFRM3Q1Kx6+PDha4a///67aZOnr0VEzd9cvOk5HDum7KpgBCsR0RSSunXriqZuV9auXdu0idtKRLSfFN1bLu50njpzjWAlooCAAKhXrx4b76CgINHETeFzYdsh3fMsdd1vM5iKiPZu4RJV3BkVFaWm0B2+FBEt3kj7M3F2ROqrMYOViKw6Hc06W61ERAtHcnYak5KSVJfusBLRAw88oLrkYdXZaoe0Hp8VTEVkNuynONOXw36sRKTti8PRl8N+rEQ0fPhw1k5jcnKy6tIdViKyO+zHDr06Pbw4U4rIHVJEPKWIDChF5A4pIp5SRAaUInKHFBFPKSIDShG5Q4qIp9dEZHdI/9WgWRytRMTZaGzUqJHqkseIESNYO41mYRdnEQ0ePJi105iQkKC6dIeViKwysjdFRLtJWKHQJRFde+utt6Bfv35+RVpaijIbF2eNZhmZRpi//fbb0LdvXze/+/TpIzKqGWiuUvfu3d1siT169BBD841QnEUUEhJimO6ePXuKaTVGsBIRTXlYuHAhzJo1y41z5swRpeCrr77Khm2HvXv3FktpWaHQImrZsqXqyv8wffp0Ns4azUTkCYymcng6xcPIXXEVkd10m4nIk1qPt0csWKWv0CKyOz3cm5gwYQIbZ412ReQtFOeSyA6sSiIr+vWwHymiqwspIj5sK0oReQFSRDyliLwDKSI/ghQRH7YVpYi8ACkinlJE3oFXRORpa40dmIXhbREVNn2e2HHx1diiRQvVFQ8rEdnZ+Niqw9MujERUVK1z3syTXiuJli9fLlaIoUUai5LU8Ub9U2awIyK62c8//zw89thjbNjUh2QHtK0KraPm6vcTTzwBnTp1AofDIfqaaOcKPaOjo2Hu3LkiDq62RFrHj/pLaESEqy0xJiZG9H098sgjrP3jjz9uugV/1apV4ZlnnmFt7ZLutVnYzZs3F6Uoly5K77Bhw0T6Ob9pGoXZVjpFAa+JiDZ94uyKgrRGmhnsiMhqxALtq2QHVCXj/NVohs2bN7M2GmlOjxmo952zs6KvR6iQQMxgNWLBp4s32hGRL7ebtCsiO2PnrGC28itte2i2sCRlBs5Oo5WI7OxW50vaHTtXbNfiliLiIUVUcEoReYFSRDyliHhKETGUIuIpRcRTiojhtSwis5WIUlNTWTuN/1QRUbo5O412d2y3ghSRC+yKiOyp05QjgZq3OX+JJCJa+oma2TlbmtimuXMlnR86dKhw52qr2dPKNfrwXMn5q9FsFSJPSPacvxo5G40kfoJRumjXcs5Oo7bxMWdvxdzcXGFrBikiF9gVkRY2jTzgaNYfQqRtRDg7uqYt30RVPlcSaP01csfZE83Cps7OU6dOQU5ODus39cdwdp4yNjZW+OPqN4V38uRJ0xELFG8uPRrNtmUhUicyZ2dFsqW+JitIEbnArojGjBnD2hUF27dvr4bCw2rEghkpE1+6dEn1yR30NufsPKVZXw0NObIz7MebtNpgjCBF5AK7IrJaY8EO7Y6dMyNlYrPvMVp8kbPzlHbWWPAlvbbGAlGKiIcUEU8pIoZSRDykiHhKETGUIuIhRcRTiojhP1VEVh/3kyZNYu2IdgdyWi0OY7X2mxULKyJP0mW2Vy2JiLPRs7D3zu49lyIyoB0RBQcHi867+Ph4N27atAn69+/P2mkkIVCHK3UgFoQ0Apv8NsO8efOgdevWrL0ZqemcMgu1khnBqiS6+eabhR+c/23btoVt27apPrmDmrnJluLhakutY/S8uTA9Je2mQf64+k2k6TpmQpMiMmBhReTpW83Mnd0hKEaTy7w56YxgJaLx48erLo1R2DimpaWxYXrKnTt3qj7xMOvslSIyoJ2SyC7NlhH2Z1iJ6LPPPlNdFj2olOfC9JRmL67z589LEXGQIip6SBEZQ4rIBVJEPKSIjCFF5AIpIh5SRMaQInKBFBEPKSJjeE1E3ux0pOZUM/iziLw9t8VboCZqLj0aaWEab8FsgRZPWkzNOnr9WkTp6eliPTDqfCxK0sNasGCBGgoPfxZR165dxbJZgwYNcuK7774L77//vulIam+C5s3QHkMUD9e40flu3bqx6dF49913i3k9rrZ2SWG/9NJLbJgaaamxmTNnsvll7NixkJmZqabSHX4rIm/3WRDMwvBXEXny1tTm71xtUEcrFx89jeLvaf+ZHZqFQWKxglF+8euSyJfw55LIjPQwfSkisxVQ/Zl2lhGWIjKAFFHBIUXE+y1FZEApIndIEfF+SxEZUIrIHVJEvN9SRAaUInKHFBHvt1dFZLXNhy8xZcoUNs4afSWiq9E6V9iWURIRFx89fdk6Z0Y7fVR0v3wmIpqJGBwcLLZH9ydSnK6//no2zhp9WRJRH9eRI0dg9+7dbtyzZ4+pCKjTsWbNmmy6a9So4VFTrxEoXAqfi9fhw4fhp59+YtOjceDAgZCVlcXaZ2RkiDk9nF1RsHr16mK3Du6+WLF+/fqmLwGvisjfaXZjfCkisxmeVrBatmrIkCGqy6IHbRDGhamRpqabgXba4+zs0tul4D9aRGb0pYjsjJ2zuxa3HdgdO0cjGjg7f6cUkQGliAoOKSJjSBG5QIqIhxSRMaSIXCBFxEOKyBimIqpUqRLrcXHnypUr1RS6g0QUEBDA2hUFaQPiwsKXDQvUIMKFqZGW6zJDu3btWDt/p7YjhRlMRUS7NlOTap06da4ZUilDO1EbgUREzeS1atVi7e0wKChIvNELC5peUqVKFdZveuF5c04PzYMyC9uqef3ZZ58VTdGcvb+SamIvv/yymgJjmIpIovihsJ2tRQFfhu1LSBFJSNiEFJGEhE1IEUlI2IQUkYSETUgRSUjYAsD/Ad35CCO79PTBAAAAAElFTkSuQmCC");
                    startScheduleQRCode(15000);
                }
            });
		}
        function reload(){
            location.reload(true);
        }
		var mouseTimer = null, cursorVisible = true;

		function disappearCursor() {
            window.clearTimeout(mouseTimer);
			mouseTimer = null;
			document.body.style.cursor = "none";
			cursorVisible = false;
		}

		document.onmousemove = function() {
			if (mouseTimer) {
				window.clearTimeout(mouseTimer);
			}
			if (!cursorVisible) {
				document.body.style.cursor = "default";
				cursorVisible = true;
			}
			mouseTimer = window.setTimeout(disappearCursor, 5000);
		};
        function currentTime() {
            $.ajax({
                type: "GET",
                url: getDateTime,
                data: {'device_token': ''},
                success: function(result) {
                    console.log(result.data)
                    document.getElementById("clock").innerText = result.data.date;
                },
                error: function(error) {
                    console.log(error)
                }
            });
            var t = setTimeout(function(){ currentTime() }, 60000);
        }
	</script>
@endsection
