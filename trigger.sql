BEGIN
	SET @textDecoded = NEW.TextDecoded;
	
	IF SUBSTRING(UPPER(@textDecoded),1,5) = "INFO " THEN 
	
		INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber, 'PESAN#ASAL#TUJUAN#WAKTU#jumlah_pesan KONFIRMASI<spasi>kode_pesanan UBAH<spasi>kode_pesanan<spasi>kode_jadwal_lama<spasi>kode_jadwal_baru<spasi>jumlah_pesan BATAL<spasi>kode_pesanan STATUS<spasi>kode_pesanan INFOJADWAL<spasi>asal<minus>tujuan KOMPLAIN<spasi>isi_komplain'); 
	
	ELSEIF SUBSTRING(UPPER(@textDecoded),1,5) = "PESAN" THEN
		
		SET @jumlah_pesan	=	SUBSTRING_INDEX(@textDecoded,"#",-1);
		#SET @temp_jam :=  SUBSTRING_INDEX(SUBSTRING_INDEX(@textDecoded,"#",-2),"#",1);
		#SET @jam		:=	CONCAT(@temp_jam,":00");
  		SET @jam =  SUBSTRING_INDEX(SUBSTRING_INDEX(@textDecoded,"#",-2),"#",1);
		SET @tujuan = UPPER(SUBSTRING_INDEX(SUBSTRING_INDEX(@textDecoded,"#",3),"#",-1));  
  		SET @asal = UPPER(SUBSTRING_INDEX(SUBSTRING_INDEX(@textDecoded,"#",2),"#",-1));
	
		SET @kode_jadwal =  (SELECT kode_jadwal FROM jadwal WHERE tujuan LIKE @tujuan and asal LIKE @asal and jam_keberangkatan LIKE @jam);
  		
		IF EXISTS(SELECT nama FROM pelanggan WHERE handphone = NEW.SenderNumber) THEN
			IF EXISTS(SELECT kode_jadwal FROM jadwal WHERE kode_jadwal = @kode_jadwal) THEN
				
				INSERT INTO reservasi (kode_jadwal, handphone, jumlah) VALUES (@kode_jadwal,NEW.SenderNumber,@jumlah_pesan); 
				SET @kode_pemesanan = (SELECT GROUP_CONCAT(id," ") FROM reservasi WHERE kode_jadwal = @kode_jadwal AND handphone = NEW.SenderNumber AND jumlah = @jumlah_pesan AND berangkat = 0);
				#INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,CONCAT("PS: Terima kasih, anda telah memesan sejumlah ",@jumlah_pesan," untuk kode tujuan ",@kode_tujuan," : ",@asal," - ",@tujuan," dengan kode pemesanan ",@kode_pemesanan,"")); 		
				INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,CONCAT(" Kode pemesanan : ",@kode_pemesanan,"")); 		
			
			#ELSE			
			
				##INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Maaf format pemesanan tidak sesuai ketik 'PESAN<spasi>ASAL<minus>TUJUAN<koma>WAKTU<koma>jumlah_pesan'. Terima kasih sebelumnya.");
				#INSERT INTO reservasi (handphone) VALUES (CONCAT("SELECT kode_jadwal FROM jadwal WHERE tujuan LIKE ",@tujuan," and asal LIKE ",@asal," and jam_keberangkatan LIKE ",@jam,""));
			END IF;
		#ELSE
			#	INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Anda belum terdaftar.");			
		END IF;
		
	ELSEIF SUBSTRING(UPPER(@textDecoded),1,10) = "KONFIRMASI" THEN
		SET @kode_jadwal := SUBSTRING_INDEX(@textDecoded," ",-1);
		SET @kode_jadwal_check := (SELECT kode_jadwal FROM reservasi WHERE id = @kode_jadwal);
		
		SET @pelanggan_check := (SELECT nama FROM pelanggan WHERE handphone = NEW.SenderNumber);
		
		IF(@pelanggan_check IS NOT NULL AND @kode_jadwal_check IS NOT NULL) THEN
				
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Terima kasih, pembayaran akan dikonfirmasi ulang."); 
			
		ELSE
				INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Anda belum terdaftar.");			
		END IF;	
		
	ELSEIF SUBSTRING(UPPER(@textDecoded),1,5) = "BATAL" THEN
		SET @kode_pesanan			:=	SUBSTRING_INDEX(@textDecoded," ",-1);
		SET @kode_pesanan_check := (SELECT id FROM reservasi WHERE id = @kode_pesanan);
		
		IF(@kode_pesanan_check IS NOT NULL) THEN
		
			UPDATE reservasi SET berangkat = 2 WHERE id = @kode_pesanan; 
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,CONCAT("PS: Terima kasih, anda telah membatalkan pemesanan dengan kode pemesanan ",@kode_pesanan));
		
		ELSE
			
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,CONCAT("PS: Maaf format pembatalan tidak sesuai atau kode pemesanan tidak ada. ketik 'BATAL <kode>' . Terima kasih sebelumnya."));
		
		END IF;	
	ELSEIF SUBSTRING(UPPER(@textDecoded),1,6) = "STATUS" THEN
		SET @kode_pesanan			:=	SUBSTRING_INDEX(@textDecoded," ",-1);
		SET @kode_pesanan_check := (SELECT berangkat FROM reservasi WHERE id = @kode_pesanan);
		
		IF(@kode_pesanan_check = 0) THEN
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Status order anda 'pending'"); 
		ELSEIF (@kode_pesanan_check = 2) THEN
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Status order anda 'batal'"); 
		ELSEIF (@kode_pesanan_check = 1) THEN
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Status order anda 'telah Berangkat'"); 
		ELSE
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Status order anda 'Belum didefinisikan'"); 
		END IF;
	ELSEIF SUBSTRING(UPPER(@textDecoded),1,10) = "INFOJADWAL" THEN
		SET @tujuan			:=	UPPER(SUBSTRING_INDEX(@textDecoded,"-",-1));
		SET @asal				:=	UPPER(SUBSTRING_INDEX(SUBSTRING_INDEX(@textDecoded,"-",1)," ",-1));
		SET @keterangan := "TIDAK DIKERTAHUI";
		#SET @check_jadwal := (select kode_jadwal from jadwal where asal = @asal and tujuan = @tujuan));
		
		IF EXISTS(select kode_jadwal from jadwal where asal = @asal and tujuan = @tujuan) THEN
			
			SET @keterangan = (SELECT GROUP_CONCAT(DATE_FORMAT(jam_keberangkatan,"%H:%m")," ") FROM JADWAL WHERE asal = @asal and tujuan = @tujuan);
			
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,CONCAT("Jam Keberangkatan ",@keterangan)); 
		ELSE
			INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"TIDAK MENEMUKAN KEBERANGKATAN"); 
		END IF;
	ELSEIF SUBSTRING(UPPER(@textDecoded),1,8) = "KOMPLAIN" THEN
		
		SET @isi_komplain				:=	SUBSTRING_INDEX(@textDecoded,"KOMPLAIN",-1);	
		INSERT INTO komplain (handphone, content) VALUES (NEW.SenderNumber,@isi_komplain);  
		INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Terima kasih atas kritik dan saran.'");  
	ELSE
		INSERT INTO outbox (DestinationNumber, TextDecoded) VALUES (NEW.SenderNumber,"PS: Maaf Format Anda tidak mengikuti format manapun. ketik 'info' kirim kembali ke sini'");  
	END IF;
END